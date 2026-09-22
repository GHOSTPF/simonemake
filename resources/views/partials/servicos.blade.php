@php($s = config('institucional.servicos'))

<section id="servicos" class="bg-branco py-24 sm:py-32">
    <div class="container-site">
        <div class="max-w-2xl" data-aos="fade-up">
            <p class="kicker">Serviços</p>
            <h2 class="titulo-secao mt-4">{{ $s['titulo'] }}</h2>
            <p class="mt-4 text-lg leading-relaxed text-grafite-700">{{ $s['subtitulo'] }}</p>
        </div>

        <div class="mt-14 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($s['pacotes'] as $pacote)
                <article data-aos="fade-up" data-aos-delay="{{ $loop->index * 90 }}"
                    class="flex h-full flex-col rounded-2xl border p-7 transition duration-300 ease-suave
                           {{ $pacote['destaque'] ? 'border-dourado bg-bege' : 'border-grafite/10 bg-branco hover:border-dourado/50' }}">
                    @if ($pacote['destaque'])
                        <span class="mb-4 inline-flex w-fit rounded-full bg-dourado px-3 py-1 text-[0.65rem] font-semibold uppercase tracking-wider text-branco">
                            Mais procurado
                        </span>
                    @endif

                    <h3 class="font-serif text-2xl">{{ $pacote['nome'] }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-grafite-700">{{ $pacote['resumo'] }}</p>

                    <ul class="mt-5 space-y-2 text-sm text-grafite-700">
                        @foreach ($pacote['inclui'] as $linha)
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5 text-dourado-700">
                                    @include('partials._icon', ['name' => 'check', 'class' => 'h-4 w-4'])
                                </span>
                                <span>{{ $linha }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <a href="#agendar"
                       @click="$dispatch('preselecionar-servico', { slug: @js($pacote['slug']) })"
                       class="mt-6 inline-flex items-center gap-1.5 border-b border-dourado pb-0.5 text-sm font-medium text-dourado-700 transition hover:border-dourado-700">
                        Reservar {{ mb_strtolower($pacote['nome']) }}
                        @include('partials._icon', ['name' => 'arrow-right', 'class' => 'h-3.5 w-3.5'])
                    </a>
                </article>
            @endforeach
        </div>

        @if (!empty($s['cursos']))
            @php($waLinkCursos = 'https://wa.me/' . preg_replace('/\D+/', '', config('institucional.marca.whatsapp_publico')) . '?text=' . rawurlencode($s['cursos']['whatsapp_texto']))
            <div data-aos="fade-up" class="mt-12 flex flex-wrap items-center justify-between gap-4 rounded-2xl bg-bege px-7 py-5">
                <p class="text-sm text-grafite-700">{{ $s['cursos']['texto'] }}</p>
                <a href="{{ $waLinkCursos }}" target="_blank" rel="noopener"
                   class="text-sm font-medium text-dourado-700 underline decoration-dourado/50 underline-offset-4 hover:decoration-dourado">
                    {{ $s['cursos']['link_texto'] }}
                </a>
            </div>
        @endif
    </div>
</section>
