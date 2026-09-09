@php($sobre = config('institucional.sobre'))

<section id="sobre" class="bg-branco py-24 sm:py-32">
    <div class="container-site grid gap-14 lg:grid-cols-12">
        <div class="lg:col-span-4" data-aos="fade-up">
            <p class="kicker">Trajetória</p>
            <h2 class="titulo-secao mt-4">{{ $sobre['titulo'] }}</h2>
            <div data-anim="linha" class="fio-dourado mt-6 max-w-[8rem]"></div>
        </div>

        <div class="lg:col-span-8" x-data="{ expandido: false }">
            <p class="max-w-prosa text-lg leading-relaxed text-grafite-700" data-aos="fade-up">
                {{ $sobre['resumo'] }}
            </p>

            <div
                x-show="expandido"
                x-cloak
                x-transition.opacity.duration.400ms
                class="mt-6 space-y-4 max-w-prosa text-grafite-700"
            >
                @foreach ($sobre['completo'] as $paragrafo)
                    <p class="leading-relaxed">{{ $paragrafo }}</p>
                @endforeach
            </div>

            <button
                type="button"
                @click="expandido = !expandido"
                class="mt-6 inline-flex items-center gap-2 border-b border-dourado pb-0.5 text-sm font-medium text-dourado-700 transition hover:border-dourado-700"
            >
                <span x-text="expandido ? @js($sobre['ler_menos']) : @js($sobre['ler_mais'])"></span>
                <span x-show="!expandido">→</span>
                <span x-show="expandido" x-cloak>↑</span>
            </button>
        </div>
    </div>
</section>
