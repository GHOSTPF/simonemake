<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();

            $table->string('nome');
            $table->string('telefone', 30);          // WhatsApp da cliente (só dígitos, formato internacional)
            $table->date('data');
            $table->time('hora');
            $table->string('tipo_servico', 40);      // slug: noiva | madrinha | festa | mechas_coloracao | outro
            $table->text('observacao')->nullable();

            $table->string('status', 20)->default('confirmado'); // confirmado | cancelado

            // ID do evento criado na Google Agenda da Simone — guardado para
            // permitir editar/cancelar o evento depois. Fica nulo se o Job ainda
            // não rodou ou se a API do Google falhou (o agendamento continua válido).
            $table->string('google_event_id')->nullable();

            $table->timestamps();

            // PROTEÇÃO FINAL CONTRA CONDIÇÃO DE CORRIDA:
            // dois envios simultâneos para a mesma data+hora — o segundo INSERT
            // falha com violação de constraint (Postgres SQLSTATE 23505), tratada
            // no AgendamentoController como HTTP 409.
            $table->unique(['data', 'hora']);

            // Consulta mais comum: "o que já existe neste dia?"
            $table->index('data');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};
