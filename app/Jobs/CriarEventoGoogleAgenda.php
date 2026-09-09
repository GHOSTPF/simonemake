<?php

namespace App\Jobs;

use App\Models\Agendamento;
use App\Services\GoogleCalendarService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Cria o evento correspondente na Google Agenda da Simone e grava o
 * google_event_id de volta no agendamento (para permitir editar/cancelar
 * o evento depois).
 *
 * Despachado com ->afterCommit(), roda na fila. Falha da API do Google não
 * reverte nada: o registro no banco continua sendo a fonte de verdade.
 */
class CriarEventoGoogleAgenda implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [30, 120];

    public function __construct(public Agendamento $agendamento)
    {
    }

    public function handle(GoogleCalendarService $calendar): void
    {
        // Se outro processamento já criou o evento, não duplica.
        if ($this->agendamento->google_event_id) {
            return;
        }

        $eventId = $calendar->criarEvento($this->agendamento);

        if ($eventId) {
            $this->agendamento->forceFill(['google_event_id' => $eventId])->save();
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Job CriarEventoGoogleAgenda falhou em definitivo.', [
            'agendamento_id' => $this->agendamento->id,
            'erro'           => $e->getMessage(),
        ]);
    }
}
