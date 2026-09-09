<?php

namespace Tests\Feature;

use App\Jobs\CriarEventoGoogleAgenda;
use App\Jobs\EnviarNotificacaoWhatsApp;
use App\Models\Agendamento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AgendamentoTest extends TestCase
{
    use RefreshDatabase;

    /** Uma quarta-feira dentro da janela da agenda (>= 24h de antecedência). */
    private function dataValida(): string
    {
        $d = Carbon::now(config('institucional.google_agenda.timezone'))
            ->addDays(10)
            ->next(Carbon::WEDNESDAY);

        return $d->format('Y-m-d');
    }

    public function test_endpoint_de_disponibilidade_devolve_slots(): void
    {
        $resp = $this->getJson('/disponibilidade?data=' . $this->dataValida() . '&tipo_servico=noiva');

        $resp->assertOk()
            ->assertJsonStructure(['data', 'slots'])
            ->assertJsonPath('slots.0', '08:00');
    }

    public function test_dia_fora_do_atendimento_nao_tem_slots(): void
    {
        // Domingo nunca é dia de atendimento no config padrão.
        $domingo = Carbon::now()->addDays(9)->next(Carbon::SUNDAY)->format('Y-m-d');

        $this->getJson('/disponibilidade?data=' . $domingo)
            ->assertOk()
            ->assertJsonPath('slots', []);
    }

    public function test_agendamento_valido_e_salvo_e_despacha_os_jobs(): void
    {
        Queue::fake();
        $data = $this->dataValida();

        $resp = $this->postJson('/agendar', [
            'nome' => 'Maria da Silva',
            'telefone' => '(83) 99999-8888',
            'data' => $data,
            'hora' => '09:00',
            'tipo_servico' => 'noiva',
            'observacao' => 'Cerimônia às 16h',
        ]);

        $resp->assertCreated()->assertJsonPath('ok', true);

        $this->assertDatabaseHas('agendamentos', [
            'nome' => 'Maria da Silva',
            'telefone' => '83999998888',
            'data' => $data . ' 00:00:00',
            'tipo_servico' => 'noiva',
            'status' => 'confirmado',
        ]);

        Queue::assertPushed(EnviarNotificacaoWhatsApp::class);
        Queue::assertPushed(CriarEventoGoogleAgenda::class);
    }

    public function test_horario_ocupado_retorna_409(): void
    {
        $data = $this->dataValida();

        Agendamento::create([
            'nome' => 'Cliente A',
            'telefone' => '83999990000',
            'data' => $data,
            'hora' => '09:00',
            'tipo_servico' => 'noiva',
            'status' => 'confirmado',
        ]);

        $this->postJson('/agendar', [
            'nome' => 'Cliente B',
            'telefone' => '83988887777',
            'data' => $data,
            'hora' => '09:00',
            'tipo_servico' => 'festa',
        ])->assertStatus(409)->assertJsonPath('code', 'horario_indisponivel');

        $this->assertSame(1, Agendamento::count());
    }

    public function test_slot_ocupado_por_noiva_some_da_lista(): void
    {
        $data = $this->dataValida();

        Agendamento::create([
            'nome' => 'Noiva X',
            'telefone' => '83999990000',
            'data' => $data,
            'hora' => '09:00',
            'tipo_servico' => 'noiva', // 180 min -> ocupa 09:00..12:00
            'status' => 'confirmado',
        ]);

        $slots = $this->getJson('/disponibilidade?data=' . $data . '&tipo_servico=noiva')
            ->json('slots');

        $this->assertNotContains('09:00', $slots);
        $this->assertNotContains('11:00', $slots); // 11:00 + 180 invadiria a reserva
        $this->assertContains('12:00', $slots);
    }

    public function test_validacao_rejeita_data_passada_e_servico_invalido(): void
    {
        $this->postJson('/agendar', [
            'nome' => 'X',
            'telefone' => '123',
            'data' => '2020-01-01',
            'hora' => '09:00',
            'tipo_servico' => 'inexistente',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'telefone', 'data', 'tipo_servico']);
    }
}
