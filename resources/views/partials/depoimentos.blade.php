@php($d = config('institucional.depoimentos'))
@if (!empty($d['itens']))
<section class="bg-branco py-24 sm:py-32">
    <div class="container-site">
        <div class="max-w-2xl" data-aos="fade-up">
            <p class="kicker">Depoimentos</p>
            <h2 class="titulo-secao mt-4">{{ $d['titulo'] }}</h2>
        </div>

        <div class="swiper mt-12" data-swiper="depoimentos">
            <div class="swiper-wrapper">
                @foreach ($d['itens'] as $item)
                    <div class="swiper-slide h-auto">
                        <figure class="flex h-full flex-col rounded-2xl border border-grafite/10 bg-bege p-8">
                            <span class="font-serif text-4xl leading-none text-dourado">&ldquo;</span>
                            <blockquote class="mt-2 flex-1 text-lg leading-relaxed text-grafite-700">
                                {{ $item['texto'] }}
                            </blockquote>
                            <figcaption class="mt-6 text-sm font-medium uppercase tracking-wider text-dourado-700">
                                {{ $item['autora'] }}
                            </figcaption>
                        </figure>
                    </div>
                @endforeach
            </div>
            <div class="mt-8 flex justify-center [&_.swiper-pagination-bullet]:bg-grafite/30 [&_.swiper-pagination-bullet-active]:!bg-dourado" data-swiper-pagination></div>
        </div>
    </div>
</section>
@endif
