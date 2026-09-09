@php($c = config('institucional.contato'))
@php($marca = config('institucional.marca'))
@php($tipos = config('institucional.agenda.tipos_servico'))
@php($waLink = 'https://wa.me/' . preg_replace('/\D+/', '', $marca['whatsapp_publico']) . '?text=' . rawurlencode($marca['whatsapp_texto_padrao']))

<section id="agendar" class="bg-bege py-24 sm:py-32">
    <div class="container-site grid gap-14 lg:grid-cols-12">
        {{-- Coluna texto --}}
        <div class="lg:col-span-5" data-aos="fade-up">
            <p class="kicker">{{ $c['kicker'] }}</p>
            <h2 class="titulo-secao mt-4">{{ $c['titulo'] }}</h2>
            <div data-anim="linha" class="fio-dourado mt-6 max-w-[8rem]"></div>
            <p class="mt-6 max-w-prosa text-lg leading-relaxed text-grafite-700">{{ $c['subtitulo'] }}</p>

            <p class="mt-6 text-sm leading-relaxed text-grafite-500">{{ $c['obs_form'] }}</p>

            <div class="mt-8 space-y-3 text-sm text-grafite-700">
                <a href="{{ $waLink }}" target="_blank" rel="noopener" class="flex items-center gap-3 hover:text-dourado-700">
                    @include('partials._icon', ['name' => 'whatsapp', 'class' => 'h-5 w-5 text-dourado-700'])
                    Prefere falar direto? Chame no WhatsApp
                </a>
                <p class="flex items-center gap-3">
                    @include('partials._icon', ['name' => 'map-pin', 'class' => 'h-5 w-5 text-dourado-700'])
                    {{ $marca['regiao'] }}
                </p>
            </div>
        </div>

        {{-- Coluna formulário --}}
        <div class="lg:col-span-7">
            <div
                x-data="agendamento"
                x-init="init()"
                @preselecionar-servico.window="form.tipo_servico = $event.detail.slug; if (form.data) buscarSlots()"
                class="rounded-3xl border border-grafite/10 bg-branco p-7 sm:p-10"
                data-aos="fade-up"
            >
                {{-- SUCESSO --}}
                <template x-if="estado === 'sucesso'">
                    <div class="py-6 text-center">
                        <span class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-full border border-dourado text-dourado-700">
                            @include('partials._icon', ['name' => 'check-circle', 'class' => 'h-8 w-8'])
                        </span>
                        <h3 class="mt-5 font-serif text-2xl">{{ $c['sucesso_titulo'] }}</h3>
                        <p class="mx-auto mt-3 max-w-sm text-grafite-700">{{ $c['sucesso_texto'] }}</p>

                        <dl class="mx-auto mt-6 max-w-xs space-y-1 rounded-xl bg-bege px-5 py-4 text-left text-sm">
                            <div class="flex justify-between gap-4"><dt class="text-grafite-500">Serviço</dt><dd x-text="resumo?.servico"></dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-grafite-500">Data</dt><dd x-text="resumo?.data"></dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-grafite-500">Horário</dt><dd x-text="resumo?.hora"></dd></div>
                        </dl>

                        <button type="button" @click="recomecar()" class="btn-secundario mt-8">Fazer outra reserva</button>
                    </div>
                </template>

                {{-- FORMULÁRIO --}}
                <form x-show="estado !== 'sucesso'" @submit.prevent="enviar()" novalidate>
                    {{-- Erro geral (409 / genérico) --}}
                    <div x-show="erroGeral" x-cloak x-transition
                         class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                         x-text="erroGeral"></div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        {{-- Nome --}}
                        <div class="sm:col-span-2">
                            <label for="ag-nome" class="campo-label">Nome</label>
                            <input id="ag-nome" type="text" x-model="form.nome" autocomplete="name"
                                   class="campo-input" :class="erros.nome && 'border-red-400'"
                                   placeholder="Seu nome completo">
                            <p class="campo-erro" x-show="erros.nome" x-cloak x-text="erros.nome"></p>
                        </div>

                        {{-- Telefone --}}
                        <div>
                            <label for="ag-tel" class="campo-label">WhatsApp</label>
                            <input id="ag-tel" type="tel" x-model="form.telefone" autocomplete="tel"
                                   class="campo-input" :class="erros.telefone && 'border-red-400'"
                                   placeholder="(83) 99999-9999">
                            <p class="campo-erro" x-show="erros.telefone" x-cloak x-text="erros.telefone"></p>
                        </div>

                        {{-- Tipo de serviço --}}
                        <div>
                            <label for="ag-servico" class="campo-label">Tipo de serviço</label>
                            <select id="ag-servico" x-model="form.tipo_servico"
                                    class="campo-input" :class="erros.tipo_servico && 'border-red-400'">
                                <option value="">Selecione…</option>
                                @foreach ($tipos as $slug => $label)
                                    <option value="{{ $slug }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="campo-erro" x-show="erros.tipo_servico" x-cloak x-text="erros.tipo_servico"></p>
                        </div>

                        {{-- Data --}}
                        <div class="sm:col-span-2">
                            <label for="ag-data" class="campo-label">Data do evento</label>
                            <input id="ag-data" type="date" x-model="form.data"
                                   :min="minData" :max="maxData"
                                   class="campo-input" :class="erros.data && 'border-red-400'">
                            <p class="campo-erro" x-show="erros.data" x-cloak x-text="erros.data"></p>
                        </div>
                    </div>

                    {{-- Horário --}}
                    <div class="mt-5">
                        <span class="campo-label">Horário</span>

                        <p x-show="!form.data" class="text-sm text-grafite-500">Escolha uma data para ver os horários livres.</p>

                        <p x-show="form.data && carregandoSlots" x-cloak class="flex items-center gap-2 text-sm text-grafite-500">
                            <svg class="h-4 w-4 animate-spin text-dourado" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"/>
                            </svg>
                            Buscando horários…
                        </p>

                        <p x-show="form.data && !carregandoSlots && slotsErro" x-cloak
                           class="text-sm text-grafite-500" x-text="slotsErro"></p>

                        <div x-show="form.data && !carregandoSlots && slots.length" x-cloak
                             class="mt-2 flex flex-wrap gap-2" role="group" aria-label="Horários disponíveis">
                            <template x-for="h in slots" :key="h">
                                <button type="button" @click="selecionarHora(h)"
                                        class="rounded-full border px-4 py-2 text-sm transition"
                                        :class="form.hora === h
                                            ? 'border-dourado bg-dourado text-branco'
                                            : 'border-grafite/20 text-grafite-700 hover:border-dourado hover:text-dourado-700'"
                                        x-text="h"></button>
                            </template>
                        </div>
                        <p class="campo-erro" x-show="erros.hora" x-cloak x-text="erros.hora"></p>
                    </div>

                    {{-- Observação --}}
                    <div class="mt-5">
                        <label for="ag-obs" class="campo-label">Observação <span class="font-normal text-grafite-500">(opcional)</span></label>
                        <textarea id="ag-obs" x-model="form.observacao" rows="3" class="campo-input"
                                  placeholder="Local do evento, nº de pessoas, horário da cerimônia…"></textarea>
                        <p class="campo-erro" x-show="erros.observacao" x-cloak x-text="erros.observacao"></p>
                    </div>

                    <button type="submit" class="btn-primario mt-8 w-full" :disabled="estado === 'enviando'">
                        <span x-show="estado !== 'enviando'">{{ $c['titulo'] }}</span>
                        <span x-show="estado === 'enviando'" x-cloak class="flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"/>
                            </svg>
                            Enviando…
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
