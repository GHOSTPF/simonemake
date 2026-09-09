<?php

namespace App\Services;

use App\Models\Agendamento;
use Illuminate\Support\Facades\Log;
use Spatie\GoogleCalendar\Event;

/**
 * Cria / remove eventos na Google Agenda da Simone.
 *
 * Autenticação: Service Account do Google Cloud com a agenda dela
 * compartilhada com o e-mail da conta de serviço (permissão
 * "Fazer alterações nos eventos"). Não há OAuth de cliente final.
 *
 * Config em config/google-calendar.php <- .env:
 *   GOOGLE_CALENDAR_SERVICE_ACCOUNT_KEY_PATH  (caminho do JSON da service account)
 *   GOOGLE_CALENDAR_ID                        (ID da agenda da Simone)
 *
 * Falhas não sobem: o agendamento no banco é a fonte de verdade. O Job que
 * chama este serviço apenas loga o erro.
 */
class GoogleCalendarService
{
    /**
     * Cria o evento e devolve o ID do Google (para guardar em
     * agendamentos.google_event_id) ou null se não foi possível criar.
     */
    public function criarEvento(Agendamento $agendamento): ?string
    {
        if (! $this->configurado()) {
            Log::warning('Google Agenda não configurada — evento não criado.', [
                'agendamento_id' => $agendamento->id,
            ]);

            return null;
        }

        $prefixo = (string) config('institucional.google_agenda.prefixo_titulo', '');

        try {
            $event = Event::create([
                'name'          => $prefixo . $agendamento->nome . ' — ' . $agendamento->servicoLabel(),
                'startDateTime' => $agendamento->inicioEm(),
                'endDateTime'   => $agendamento->fimEm(),
                'description'   => $this->montarDescricao($agendamento),
            ]);

            return $event->id;
        } catch (\Throwable $e) {
            Log::error('Falha ao criar evento na Google Agenda.', [
                'agendamento_id' => $agendamento->id,
                'erro'           => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Remove o evento correspondente (usar ao cancelar um agendamento).
     */
    public function removerEvento(string $googleEventId): bool
    {
        if (! $this->configurado()) {
            return false;
        }

        try {
            $evento = Event::find($googleEventId);
            $evento?->delete();

            return true;
        } catch (\Throwable $e) {
            Log::error('Falha ao remover evento da Google Agenda.', [
                'google_event_id' => $googleEventId,
                'erro'            => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Há credenciais utilizáveis? Evita quebrar o ambiente de dev sem chaves.
     */
    public function configurado(): bool
    {
        $profile = config('google-calendar.default_auth_profile');
        $path    = config("google-calendar.auth_profiles.{$profile}.credentials_json")
            ?? config('google-calendar.service_account_credentials_json');

        return ! empty(config('google-calendar.calendar_id'))
            && is_string($path)
            && file_exists($path);
    }

    private function montarDescricao(Agendamento $agendamento): string
    {
        $linhas = [
            'Agendamento feito pelo site.',
            '',
            'Cliente: ' . $agendamento->nome,
            'WhatsApp: ' . $agendamento->telefone,
            'Serviço: ' . $agendamento->servicoLabel(),
        ];

        if ($agendamento->observacao) {
            $linhas[] = '';
            $linhas[] = 'Observação: ' . $agendamento->observacao;
        }

        return implode("\n", $linhas);
    }
}
