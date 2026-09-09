<?php

namespace App\Jobs;

use App\Models\Agendamento;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Notifica a Simone (e, se houver template aprovado, a cliente) sobre um novo
 * agendamento. Despachado com ->afterCommit() pelo AgendamentoController, roda
 * na fila para não segurar a resposta HTTP.
 *
 * Falha de API não deve reverter o agendamento: capturamos, logamos e seguimos.
 */
class EnviarNotificacaoWhatsApp implements ShouldQueue
{
    use Queueable;

    /** Tentativas antes de desistir (a falha final só é logada). */
    public int $tries = 3;

    /** Espera entre tentativas (segundos). */
    public array $backoff = [30, 120];

    public function __construct(public Agendamento $agendamento)
    {
    }

    public function handle(WhatsAppService $whatsapp): void
    {
        $whatsapp->notificarSimone($this->agendamento);

        // Só envia se houver template pré-aprovado configurado (ver config).
        $whatsapp->confirmarParaCliente($this->agendamento);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Job EnviarNotificacaoWhatsApp falhou em definitivo.', [
            'agendamento_id' => $this->agendamento->id,
            'erro'           => $e->getMessage(),
        ]);
    }
}
