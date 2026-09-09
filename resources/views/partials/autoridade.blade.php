@php($a = config('institucional.autoridade'))

<section id="autoridade" class="bg-bege py-24 sm:py-32">
    <div class="container-site">
        <div class="max-w-2xl" data-aos="fade-up">
            <p class="kicker">Autoridade &amp; provas</p>
            <h2 class="titulo-secao mt-4">{{ $a['titulo'] }}</h2>
            <p class="mt-4 text-lg leading-relaxed text-grafite-700">{{ $a['subtitulo'] }}</p>
        </div>

        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($a['cards'] as $card)
                <article data-aos="fade-up" data-aos-delay="{{ $loop->index * 90 }}"
                         class="group flex h-full flex-col rounded-2xl border border-grafite/10 bg-branco p-7 transition duration-300 ease-suave hover:-translate-y-1 hover:border-dourado/60">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-dourado/40 text-dourado-700">
                        @include('partials._icon', ['name' => $card['icone'], 'class' => 'h-6 w-6'])
                    </span>
                    <h3 class="mt-5 font-serif text-xl">{{ $card['titulo'] }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-grafite-700">{{ $card['descricao'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
