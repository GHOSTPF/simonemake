<?php

namespace App\Services;

use App\Models\Agendamento;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Calcula os horários livres de um dia.
 *
 * Regras (todas configuráveis em config/institucional.php -> 'agenda'):
 *  - só dias em 'dias_atendimento';
 *  - dentro da janela 'abertura'..'fechamento';
 *  - slots começam de 'intervalo_slots' em 'intervalo_slots' minutos;
 *  - respeita 'antecedencia_minima_horas' e 'janela_futura_dias';
 *  - datas em 'bloqueios' ficam indisponíveis;
 *  - um slot só entra se o atendimento (início + duração do tipo de serviço)
 *    couber na janela E não colidir com nenhum agendamento já confirmado.
 */
class DisponibilidadeService
{
    /**
     * @return array<int, string> horários "HH:MM" livres para a data.
     */
    public function slotsDisponiveis(Carbon $data, ?string $tipoServico = null): array
    {
        $cfg = config('institucional.agenda');
        $tz  = config('institucional.google_agenda.timezone', config('app.timezone'));

        // Reinterpreta a data como data CIVIL no fuso da agenda — sem deslocar o
        // dia. (Um "2026-09-16" recebido como meia-noite UTC viraria dia 15 ao
        // converter para um fuso atrás de UTC, como America/Recife.)
        $data = Carbon::createFromFormat('Y-m-d', $data->format('Y-m-d'), $tz)->startOfDay();

        if (! $this->dataAtendivel($data, $cfg, $tz)) {
            return [];
        }

        $duracao = Agendamento::duracaoParaServico($tipoServico);

        $abertura   = $this->horaNoDia($data, $cfg['abertura']);
        $fechamento = $this->horaNoDia($data, $cfg['fechamento']);
        $passo      = max(5, (int) $cfg['intervalo_slots']);

        $ocupados      = $this->intervalosOcupados($data);
        $agoraComMargem = Carbon::now($tz)->addHours((int) $cfg['antecedencia_minima_horas']);

        $slots  = [];
        $cursor = $abertura->copy();

        while ($cursor->copy()->addMinutes($duracao)->lessThanOrEqualTo($fechamento)) {
            $inicio = $cursor->copy();
            $fim    = $cursor->copy()->addMinutes($duracao);

            $passouAntecedencia = $inicio->greaterThanOrEqualTo($agoraComMargem);
            $livre = $passouAntecedencia && ! $this->colide($inicio, $fim, $ocupados);

            if ($livre) {
                $slots[] = $inicio->format('H:i');
            }

            $cursor->addMinutes($passo);
        }

        return $slots;
    }

    /**
     * Um horário específico está livre para este tipo de serviço?
     * Usado como checagem rápida antes da transação (a proteção real é o
     * lock + a constraint UNIQUE no banco).
     */
    public function horarioLivre(Carbon $data, string $hora, ?string $tipoServico = null): bool
    {
        return in_array(
            Carbon::parse($hora)->format('H:i'),
            $this->slotsDisponiveis($data, $tipoServico),
            true
        );
    }

    private function dataAtendivel(Carbon $data, array $cfg, string $tz): bool
    {
        $hoje = Carbon::today($tz);

        if ($data->lessThan($hoje)) {
            return false;
        }

        if ($data->greaterThan($hoje->copy()->addDays((int) $cfg['janela_futura_dias']))) {
            return false;
        }

        if (! in_array($data->dayOfWeekIso, (array) $cfg['dias_atendimento'], true)) {
            return false;
        }

        if (in_array($data->format('Y-m-d'), (array) ($cfg['bloqueios'] ?? []), true)) {
            return false;
        }

        return true;
    }

    /**
     * Intervalos [inicio, fim) já ocupados no dia, calculados a partir da
     * duração de cada agendamento confirmado.
     *
     * @return Collection<int, array{0: Carbon, 1: Carbon}>
     */
    private function intervalosOcupados(Carbon $data): Collection
    {
        return Agendamento::query()
            ->whereDate('data', $data->format('Y-m-d'))
            ->where('status', Agendamento::STATUS_CONFIRMADO)
            ->get(['hora', 'tipo_servico'])
            ->map(function (Agendamento $ag) use ($data) {
                $inicio = $this->horaNoDia($data, substr((string) $ag->hora, 0, 5));
                $fim    = $inicio->copy()->addMinutes($ag->duracaoMinutos());

                return [$inicio, $fim];
            });
    }

    /**
     * @param Collection<int, array{0: Carbon, 1: Carbon}> $ocupados
     */
    private function colide(Carbon $inicio, Carbon $fim, Collection $ocupados): bool
    {
        foreach ($ocupados as [$oInicio, $oFim]) {
            // sobreposição de intervalos semiabertos
            if ($inicio->lessThan($oFim) && $fim->greaterThan($oInicio)) {
                return true;
            }
        }

        return false;
    }

    private function horaNoDia(Carbon $data, string $hhmm): Carbon
    {
        [$h, $m] = array_pad(explode(':', $hhmm), 2, '00');

        return $data->copy()->setTime((int) $h, (int) $m, 0);
    }
}
