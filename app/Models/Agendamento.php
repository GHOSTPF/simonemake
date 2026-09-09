<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property string      $nome
 * @property string      $telefone
 * @property \Carbon\Carbon $data
 * @property string      $hora        ("HH:MM:SS")
 * @property string      $tipo_servico
 * @property string|null $observacao
 * @property string      $status
 * @property string|null $google_event_id
 */
class Agendamento extends Model
{
    protected $table = 'agendamentos';

    protected $fillable = [
        'nome',
        'telefone',
        'data',
        'hora',
        'tipo_servico',
        'observacao',
        'status',
        'google_event_id',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public const STATUS_CONFIRMADO = 'confirmado';
    public const STATUS_CANCELADO  = 'cancelado';

    /**
     * Duração deste agendamento em minutos, conforme config/institucional.php.
     */
    public function duracaoMinutos(): int
    {
        return self::duracaoParaServico($this->tipo_servico);
    }

    /**
     * Duração (min) de um tipo de serviço — lê o mapa de config e cai no padrão.
     */
    public static function duracaoParaServico(?string $tipoServico): int
    {
        $mapa   = (array) config('institucional.agenda.duracao_por_servico', []);
        $padrao = (int) config('institucional.agenda.duracao_padrao', 60);

        return (int) ($mapa[$tipoServico] ?? $padrao);
    }

    /**
     * Início do atendimento como Carbon (data + hora), no timezone da agenda.
     */
    public function inicioEm(): Carbon
    {
        $tz = config('institucional.google_agenda.timezone', config('app.timezone'));

        return Carbon::parse(
            $this->data->format('Y-m-d') . ' ' . substr((string) $this->hora, 0, 5),
            $tz
        );
    }

    /**
     * Fim do atendimento (início + duração do serviço).
     */
    public function fimEm(): Carbon
    {
        return $this->inicioEm()->addMinutes($this->duracaoMinutos());
    }

    /**
     * Rótulo legível do serviço ("Noiva", "Mechas e coloração"...).
     */
    public function servicoLabel(): string
    {
        $tipos = (array) config('institucional.agenda.tipos_servico', []);

        return $tipos[$this->tipo_servico] ?? ucfirst((string) $this->tipo_servico);
    }
}
