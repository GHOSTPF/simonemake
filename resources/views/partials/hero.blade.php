@php($hero = config('institucional.hero'))
@php($marca = config('institucional.marca'))
@php($temImagem = file_exists(public_path($hero['imagem'])))

<section data-hero class="relative isolate flex min-h-[100svh] items-center overflow-hidden bg-bege">
    {{-- Mídia de fundo --}}
    <div data-hero-media class="absolute inset-0 -z-10">
        @if ($temImagem)
            <img src="{{ asset($hero['imagem']) }}" alt="{{ $hero['imagem_alt'] }}"
                 class="h-full w-full object-cover object-center">
            <div class="absolute inset-0 bg-gradient-to-r from-branco/90 via-branco/70 to-branco/20"></div>
        @else
            {{-- Placeholder sóbrio enquanto não há foto (troque em config/institucional.php -> hero.imagem) --}}
            <div class="h-full w-full bg-[radial-gradient(120%_120%_at_0%_0%,#EFE6DA_0%,#D9C7A6_60%,#EFE6DA_100%)]"></div>
        @endif
    </div>

    <div class="container-site py-32">
        <div class="max-w-2xl">
            <p data-hero-kicker class="kicker">{{ $hero['kicker'] }}</p>

            <h1 data-hero-titulo class="mt-6 font-serif text-4xl leading-[1.08] sm:text-5xl md:text-6xl">
                @foreach (explode(',', $hero['titulo']) as $i => $pedaco)
                    <span class="block overflow-hidden"><span class="linha inline-block">{{ trim($pedaco) }}{{ $loop->last ? '' : ',' }}</span></span>
                @endforeach
            </h1>

            <div data-hero-titulo-fio class="mt-6 h-px w-24 bg-dourado"></div>

            <p data-hero-sub class="mt-6 max-w-prosa text-lg leading-relaxed text-grafite-700">
                {{ $hero['subtitulo'] }}
            </p>

            <div class="mt-9 flex flex-wrap items-center gap-4">
                <a data-hero-cta href="#agendar" class="btn-primario">
                    {{ $hero['cta_texto'] }}
                    @include('partials._icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4'])
                </a>
                <a data-hero-cta href="#trabalhos" class="btn-secundario">{{ $hero['cta_secundario'] }}</a>
            </div>

            <a data-hero-cta href="{{ $marca['instagram_url'] }}" target="_blank" rel="noopener"
               class="mt-8 inline-flex items-center gap-2 text-sm text-grafite-700 transition hover:text-dourado-700">
                @include('partials._icon', ['name' => 'instagram', 'class' => 'h-4 w-4'])
                {{ $marca['instagram_user'] }}
            </a>
        </div>
    </div>
</section>
