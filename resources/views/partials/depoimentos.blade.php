@php($d = config('institucional.depoimentos'))
@if (!empty($d['itens']))
<section class="bg-branco py-24 sm:py-32">
    <div class="container-site">
        <div class="max-w-2xl" data-aos="fade-up">
            <p class="kicker">Depoimentos</p>
            <h2 class="titulo-secao mt-4">{{ $d['titulo'] }}</h2>
        </div>

        @php($coresAvatar = ['bg-dourado text-branco', 'bg-grafite text-branco', 'bg-dourado-700 text-branco'])

        <div class="swiper mt-12" data-swiper="depoimentos">
            <div class="swiper-wrapper">
                @foreach ($d['itens'] as $i => $item)
                    <div class="swiper-slide h-auto">
                        <figure class="flex h-full flex-col rounded-2xl border border-grafite/10 bg-bege p-5">
                            <figcaption class="flex items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-semibold {{ $coresAvatar[$i % count($coresAvatar)] }}">
                                    {{ mb_substr($item['nome'], 0, 1) }}
                                </span>
                                <span class="min-w-0 truncate text-sm font-medium text-grafite">{{ $item['nome'] }}</span>
                            </figcaption>
                            <blockquote class="mt-3 flex-1 text-sm leading-relaxed text-grafite-700">
                                {{ $item['texto'] }}
                            </blockquote>
                        </figure>
                    </div>
                @endforeach
            </div>
            <div class="mt-8 flex justify-center [&_.swiper-pagination-bullet]:bg-grafite/30 [&_.swiper-pagination-bullet-active]:!bg-dourado" data-swiper-pagination></div>
        </div>
    </div>
</section>
@endif
