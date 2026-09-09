@php($p = config('institucional.portfolio'))

<section id="trabalhos" class="bg-bege py-24 sm:py-32"
         x-data="{ aberto: false, atual: 0, imagens: @js(collect($p['imagens'])->map(fn ($i) => ['src' => asset($i['src']), 'alt' => $i['alt'], 'tipo' => $i['tipo']])->values()),
                   abrir(i) { this.atual = i; this.aberto = true; window.lenis?.stop(); },
                   fechar() { this.aberto = false; window.lenis?.start(); },
                   prox() { this.atual = (this.atual + 1) % this.imagens.length; },
                   ant() { this.atual = (this.atual - 1 + this.imagens.length) % this.imagens.length; } }"
         @keydown.escape.window="fechar()"
         @keydown.arrow-right.window="aberto && prox()"
         @keydown.arrow-left.window="aberto && ant()">
    <div class="container-site">
        <div class="flex flex-wrap items-end justify-between gap-6" data-aos="fade-up">
            <div class="max-w-2xl">
                <p class="kicker">Prova visual</p>
                <h2 class="titulo-secao mt-4">{{ $p['titulo'] }}</h2>
                <p class="mt-4 text-lg leading-relaxed text-grafite-700">{{ $p['subtitulo'] }}</p>
            </div>
            <div class="flex gap-2">
                <button type="button" data-swiper-prev aria-label="Anterior"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-grafite/20 text-grafite transition hover:border-dourado hover:text-dourado-700">
                    @include('partials._icon', ['name' => 'chevron-left', 'class' => 'h-5 w-5'])
                </button>
                <button type="button" data-swiper-next aria-label="Próximo"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-grafite/20 text-grafite transition hover:border-dourado hover:text-dourado-700">
                    @include('partials._icon', ['name' => 'chevron-right', 'class' => 'h-5 w-5'])
                </button>
            </div>
        </div>

        <div class="swiper mt-12" data-swiper="portfolio">
            <div class="swiper-wrapper">
                @foreach ($p['imagens'] as $i => $img)
                    @php($existe = file_exists(public_path($img['src'])))
                    <div class="swiper-slide">
                        <button type="button" @click="abrir({{ $i }})"
                                class="group relative block aspect-[4/5] w-full overflow-hidden rounded-2xl bg-areia">
                            @if ($existe)
                                <img src="{{ asset($img['src']) }}" alt="{{ $img['alt'] }}" loading="lazy"
                                     class="h-full w-full object-cover transition duration-700 ease-suave group-hover:scale-105">
                            @else
                                <span class="flex h-full w-full items-center justify-center text-sm text-grafite-500">
                                    {{ $img['src'] }}
                                </span>
                            @endif
                            <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-grafite/70 to-transparent p-4 text-left text-xs uppercase tracking-wider text-branco opacity-0 transition group-hover:opacity-100">
                                {{ $img['tipo'] }}
                            </span>
                        </button>
                    </div>
                @endforeach
            </div>
            <div class="mt-8 flex justify-center [&_.swiper-pagination-bullet]:bg-grafite/30 [&_.swiper-pagination-bullet-active]:!bg-dourado" data-swiper-pagination></div>
        </div>
    </div>

    {{-- Lightbox --}}
    <div x-show="aberto" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-grafite/90 p-4 sm:p-8"
         @click.self="fechar()">
        <button type="button" @click="fechar()" aria-label="Fechar"
                class="absolute right-4 top-4 inline-flex h-11 w-11 items-center justify-center rounded-full bg-branco/10 text-branco hover:bg-branco/20">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
        <button type="button" @click="ant()" aria-label="Anterior"
                class="absolute left-2 sm:left-6 inline-flex h-12 w-12 items-center justify-center rounded-full bg-branco/10 text-branco hover:bg-branco/20">
            @include('partials._icon', ['name' => 'chevron-left', 'class' => 'h-6 w-6'])
        </button>
        <button type="button" @click="prox()" aria-label="Próximo"
                class="absolute right-2 sm:right-6 inline-flex h-12 w-12 items-center justify-center rounded-full bg-branco/10 text-branco hover:bg-branco/20">
            @include('partials._icon', ['name' => 'chevron-right', 'class' => 'h-6 w-6'])
        </button>

        <figure class="max-h-full max-w-3xl">
            <template x-if="imagens[atual]">
                <img :src="imagens[atual].src" :alt="imagens[atual].alt"
                     class="mx-auto max-h-[80vh] w-auto rounded-lg object-contain">
            </template>
            <figcaption class="mt-3 text-center text-sm text-bege/80" x-text="imagens[atual]?.tipo"></figcaption>
        </figure>
    </div>
</section>
