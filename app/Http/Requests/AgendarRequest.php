<?php

namespace App\Http\Requests;

use App\Models\Agendamento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Validação server-side do formulário "Reserve sua data".
 * A validação do Alpine no front é só conveniência — esta é a que conta.
 */
class AgendarRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Site público, sem login.
        return true;
    }

    public function rules(): array
    {
        $tiposValidos = array_keys((array) config('institucional.agenda.tipos_servico', []));
        $janelaDias   = (int) config('institucional.agenda.janela_futura_dias', 120);

        return [
            'nome' => ['required', 'string', 'min:2', 'max:120'],

            // Aceita máscara "(83) 99999-9999" no envio; normalizamos em prepareForValidation.
            'telefone' => ['required', 'string', 'regex:/^\d{10,13}$/'],

            'data' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:today',
                'before_or_equal:' . now()->addDays($janelaDias)->format('Y-m-d'),
            ],

            'hora' => ['required', 'date_format:H:i'],

            'tipo_servico' => ['required', Rule::in($tiposValidos)],

            'observacao' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nome'     => trim((string) $this->input('nome')),
            'telefone' => $this->somenteDigitos((string) $this->input('telefone')),
            'hora'     => $this->input('hora') ? substr((string) $this->input('hora'), 0, 5) : null,
        ]);
    }

    /**
     * Regras que dependem de mais de um campo / do calendário configurado.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }

            $data = Carbon::createFromFormat('Y-m-d', $this->input('data'));
            $cfg  = config('institucional.agenda');

            // Dia da semana atendido?
            if (! in_array($data->dayOfWeekIso, (array) $cfg['dias_atendimento'], true)) {
                $v->errors()->add('data', 'A Simone não atende nesse dia da semana.');

                return;
            }

            // Data bloqueada manualmente (férias/feriado)?
            if (in_array($data->format('Y-m-d'), (array) ($cfg['bloqueios'] ?? []), true)) {
                $v->errors()->add('data', 'Essa data não está disponível.');

                return;
            }

            // Antecedência mínima.
            $tz    = config('institucional.google_agenda.timezone', config('app.timezone'));
            $quando = Carbon::parse($this->input('data') . ' ' . $this->input('hora'), $tz);
            $minimo = Carbon::now($tz)->addHours((int) $cfg['antecedencia_minima_horas']);

            if ($quando->lessThan($minimo)) {
                $v->errors()->add('hora', 'Escolha um horário com mais antecedência.');

                return;
            }

            // O horário cabe na janela de funcionamento, considerando a duração
            // do serviço? (checagem de ocupação real fica no controller, sob lock)
            $duracao    = Agendamento::duracaoParaServico($this->input('tipo_servico'));
            $fechamento = Carbon::parse($this->input('data') . ' ' . $cfg['fechamento'], $tz);

            if ($quando->copy()->addMinutes($duracao)->greaterThan($fechamento)) {
                $v->errors()->add('hora', 'Não há tempo suficiente antes do fim do expediente para esse serviço.');
            }
        });
    }

    /**
     * Mensagens em pt-BR. O Laravel não traz o pacote de idioma pt_BR por
     * padrão, então localizamos aqui as regras que a visitante pode ver.
     */
    public function messages(): array
    {
        return [
            'required'               => 'Preencha o campo :attribute.',
            'nome.min'               => 'Informe seu nome completo.',
            'nome.max'               => 'Nome muito longo.',
            'telefone.regex'         => 'Informe um WhatsApp válido com DDD.',
            'data.date_format'       => 'Data inválida.',
            'data.after_or_equal'    => 'Escolha uma data futura.',
            'data.before_or_equal'   => 'Essa data está fora da agenda aberta.',
            'hora.date_format'       => 'Horário inválido.',
            'tipo_servico.in'        => 'Selecione um tipo de serviço válido.',
            'observacao.max'         => 'Observação muito longa (máx. 1000 caracteres).',
        ];
    }

    public function attributes(): array
    {
        return [
            'nome'         => 'nome',
            'telefone'     => 'WhatsApp',
            'data'         => 'data',
            'hora'         => 'horário',
            'tipo_servico' => 'tipo de serviço',
            'observacao'   => 'observação',
        ];
    }

    private function somenteDigitos(string $valor): string
    {
        return preg_replace('/\D+/', '', $valor) ?? '';
    }
}
