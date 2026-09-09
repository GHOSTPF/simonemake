<?php

namespace App\Http\Controllers;

use App\Exceptions\HorarioIndisponivelException;
use App\Http\Requests\AgendarRequest;
use App\Jobs\CriarEventoGoogleAgenda;
use App\Jobs\EnviarNotificacaoWhatsApp;
use App\Models\Agendamento;
use App\Services\DisponibilidadeService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AgendamentoController extends Controller
{
    public function __construct(private DisponibilidadeService $disponibilidade)
    {
    }

    /**
     * GET /disponibilidade?data=YYYY-MM-DD&tipo_servico=noiva
     *
     * Consumido pelo Alpine via fetch antes de mostrar o seletor de horário.
     * Devolve só os horários realmente livres — a visitante nunca vê um
     * horário ocupado.
     */
    public function disponibilidade(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'data'         => ['required', 'date_format:Y-m-d'],
            'tipo_servico' => ['nullable', 'string'],
        ]);

        $data = Carbon::createFromFormat('Y-m-d', $dados['data'])->startOfDay();

        $slots = $this->disponibilidade->slotsDisponiveis(
            $data,
            $dados['tipo_servico'] ?? null
        );

        return response()->json([
            'data'  => $data->format('Y-m-d'),
            'slots' => $slots,
        ]);
    }

    /**
     * POST /agendar
     *
     * Fluxo:
     *  1. valida (AgendarRequest);
     *  2. transação com lockForUpdate(): confere se data+hora está livre e insere;
     *  3. a constraint UNIQUE(data, hora) é a rede de segurança final;
     *  4. após o COMMIT, despacha os Jobs de WhatsApp e Google Agenda na fila.
     *
     * Respostas:
     *  201 -> agendado
     *  409 -> horário já ocupado
     *  422 -> validação (tratada pelo AgendarRequest)
     */
    public function store(AgendarRequest $request): JsonResponse
    {
        $dados = $request->validated();

        // Checagem rápida e amigável antes de abrir a transação.
        $data = Carbon::createFromFormat('Y-m-d', $dados['data'])->startOfDay();

        if (! $this->disponibilidade->horarioLivre($data, $dados['hora'], $dados['tipo_servico'])) {
            return $this->respostaConflito();
        }

        try {
            $agendamento = DB::transaction(function () use ($dados) {
                // Trava as linhas do dia para serializar envios concorrentes.
                $conflito = Agendamento::query()
                    ->where('data', $dados['data'])
                    ->where('hora', $dados['hora'])
                    ->lockForUpdate()
                    ->exists();

                if ($conflito) {
                    throw new HorarioIndisponivelException();
                }

                return Agendamento::create([
                    'nome'         => $dados['nome'],
                    'telefone'     => $dados['telefone'],
                    'data'         => $dados['data'],
                    'hora'         => $dados['hora'],
                    'tipo_servico' => $dados['tipo_servico'],
                    'observacao'   => $dados['observacao'] ?? null,
                    'status'       => Agendamento::STATUS_CONFIRMADO,
                ]);
            });
        } catch (HorarioIndisponivelException) {
            return $this->respostaConflito();
        } catch (QueryException $e) {
            // 23505 = unique_violation no PostgreSQL (rede de segurança contra corrida).
            if ($this->violacaoDeUnicidade($e)) {
                return $this->respostaConflito();
            }

            throw $e;
        }

        // Só depois do COMMIT: dispara integrações externas na fila.
        // afterCommit() é redundante aqui (já estamos fora da transação) mas
        // deixa a intenção explícita e protege caso o dispatch seja movido.
        EnviarNotificacaoWhatsApp::dispatch($agendamento)->afterCommit();
        CriarEventoGoogleAgenda::dispatch($agendamento)->afterCommit();

        return response()->json([
            'ok'      => true,
            'message' => config('institucional.contato.sucesso_texto'),
            'agendamento' => [
                'nome'    => $agendamento->nome,
                'data'    => $agendamento->data->format('d/m/Y'),
                'hora'    => substr((string) $agendamento->hora, 0, 5),
                'servico' => $agendamento->servicoLabel(),
            ],
        ], 201);
    }

    private function respostaConflito(): JsonResponse
    {
        return response()->json([
            'ok'      => false,
            'code'    => 'horario_indisponivel',
            'message' => config('institucional.contato.erro_conflito'),
        ], 409);
    }

    private function violacaoDeUnicidade(QueryException $e): bool
    {
        // PostgreSQL expõe o SQLSTATE em getCode(); 23505 = unique_violation.
        // Fallback textual cobre outros drivers em ambiente de dev (sqlite/mysql).
        return $e->getCode() === '23505'
            || str_contains(strtolower($e->getMessage()), 'unique')
            || str_contains(strtolower($e->getMessage()), 'duplicate');
    }
}
