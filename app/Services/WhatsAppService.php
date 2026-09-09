<?php

namespace App\Services;

use App\Models\Agendamento;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Envio de mensagens pela WhatsApp Cloud API da Meta.
 *
 * Credenciais em config/services.php ('whatsapp') <- .env:
 *   WHATSAPP_CLOUD_API_TOKEN   (token permanente do app / System User)
 *   WHATSAPP_PHONE_NUMBER_ID   (ID do número remetente, não é o telefone)
 *   WHATSAPP_NOTIFY_NUMBER     (telefone da Simone que recebe o aviso)
 *
 * Nenhuma exceção sobe daqui: o agendamento no banco é a fonte de verdade.
 * Falha de envio é logada e o fluxo segue.
 */
class WhatsAppService
{
    private string $version = 'v21.0';

    private ?string $token;
    private ?string $phoneNumberId;

    public function __construct()
    {
        $this->token         = config('services.whatsapp.token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
    }

    /**
     * Avisa a Simone sobre um novo agendamento (mensagem de texto simples —
     * permitido porque é um número próprio que optou por receber).
     */
    public function notificarSimone(Agendamento $agendamento): bool
    {
        $destino = config('services.whatsapp.notify_number');

        if (! $this->configurado() || empty($destino)) {
            Log::warning('WhatsApp não configurado — aviso de agendamento não enviado.', [
                'agendamento_id' => $agendamento->id,
            ]);

            return false;
        }

        $texto = $this->montarMensagemSimone($agendamento);

        return $this->enviarTexto($destino, $texto, $agendamento->id);
    }

    /**
     * Confirmação automática para a CLIENTE.
     *
     * Fora da janela de 24h a Meta exige um TEMPLATE pré-aprovado. Enquanto
     * não houver template configurado (config institucional.whatsapp.template_cliente.nome),
     * este método não faz nada — por design.
     */
    public function confirmarParaCliente(Agendamento $agendamento): bool
    {
        $template = config('institucional.whatsapp.template_cliente');

        if (empty($template['nome'])) {
            // Sem template aprovado: não é erro, apenas não enviamos.
            return false;
        }

        if (! $this->configurado()) {
            return false;
        }

        $componentes = [[
            'type'       => 'body',
            'parameters' => collect($template['parametros'] ?? [])
                ->map(fn ($campo) => ['type' => 'text', 'text' => (string) $this->valorParametro($agendamento, $campo)])
                ->all(),
        ]];

        return $this->enviarTemplate(
            $agendamento->telefone,
            $template['nome'],
            $template['idioma'] ?? 'pt_BR',
            $componentes,
            $agendamento->id
        );
    }

    public function configurado(): bool
    {
        return ! empty($this->token) && ! empty($this->phoneNumberId);
    }

    // ---------------------------------------------------------------------
    // Chamadas HTTP
    // ---------------------------------------------------------------------

    private function enviarTexto(string $para, string $texto, ?int $agendamentoId = null): bool
    {
        return $this->post([
            'messaging_product' => 'whatsapp',
            'to'                => $this->normalizarNumero($para),
            'type'              => 'text',
            'text'              => ['preview_url' => false, 'body' => $texto],
        ], $agendamentoId);
    }

    private function enviarTemplate(string $para, string $nome, string $idioma, array $componentes, ?int $agendamentoId = null): bool
    {
        return $this->post([
            'messaging_product' => 'whatsapp',
            'to'                => $this->normalizarNumero($para),
            'type'              => 'template',
            'template'          => [
                'name'       => $nome,
                'language'   => ['code' => $idioma],
                'components' => $componentes,
            ],
        ], $agendamentoId);
    }

    private function post(array $payload, ?int $agendamentoId): bool
    {
        $url = "https://graph.facebook.com/{$this->version}/{$this->phoneNumberId}/messages";

        try {
            $resposta = Http::withToken($this->token)
                ->acceptJson()
                ->timeout(15)
                ->post($url, $payload);

            if ($resposta->successful()) {
                return true;
            }

            Log::error('WhatsApp Cloud API retornou erro.', [
                'agendamento_id' => $agendamentoId,
                'status'         => $resposta->status(),
                'body'           => $resposta->json() ?? $resposta->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Falha ao chamar a WhatsApp Cloud API.', [
                'agendamento_id' => $agendamentoId,
                'erro'           => $e->getMessage(),
            ]);
        }

        return false;
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    private function montarMensagemSimone(Agendamento $agendamento): string
    {
        $template = (string) config('institucional.whatsapp.mensagem_simone');

        return strtr($template, [
            ':nome'       => $agendamento->nome,
            ':telefone'   => $this->formatarTelefoneHumano($agendamento->telefone),
            ':servico'    => $agendamento->servicoLabel(),
            ':data'       => $agendamento->data->format('d/m/Y'),
            ':hora'       => substr((string) $agendamento->hora, 0, 5),
            ':observacao' => $agendamento->observacao ?: '—',
        ]);
    }

    private function valorParametro(Agendamento $agendamento, string $campo): string
    {
        return match ($campo) {
            'nome'     => $agendamento->nome,
            'telefone' => $this->formatarTelefoneHumano($agendamento->telefone),
            'servico'  => $agendamento->servicoLabel(),
            'data'     => $agendamento->data->format('d/m/Y'),
            'hora'     => substr((string) $agendamento->hora, 0, 5),
            default    => (string) ($agendamento->{$campo} ?? ''),
        };
    }

    /** Só dígitos, com DDI 55 quando vier sem. */
    private function normalizarNumero(string $numero): string
    {
        $digitos = preg_replace('/\D+/', '', $numero) ?? '';

        if (strlen($digitos) <= 11) {
            $digitos = '55' . $digitos;
        }

        return $digitos;
    }

    private function formatarTelefoneHumano(string $numero): string
    {
        $d = preg_replace('/\D+/', '', $numero) ?? '';
        $d = preg_replace('/^55/', '', $d);

        if (strlen($d) === 11) {
            return sprintf('(%s) %s-%s', substr($d, 0, 2), substr($d, 2, 5), substr($d, 7));
        }
        if (strlen($d) === 10) {
            return sprintf('(%s) %s-%s', substr($d, 0, 2), substr($d, 2, 4), substr($d, 6));
        }

        return $numero;
    }
}
