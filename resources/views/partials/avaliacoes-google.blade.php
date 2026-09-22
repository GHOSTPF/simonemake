@php($g = config('institucional.avaliacoes_google'))
@if (!empty($g))
<section id="avaliacoes" class="bg-branco py-24 sm:py-32">
    <div class="container-site">
        <div class="mx-auto max-w-2xl text-center" data-aos="fade-up">
            <p class="kicker">{{ $g['kicker'] }}</p>
            <h2 class="titulo-secao mt-4">{{ $g['titulo'] }}</h2>
        </div>

        <div class="mx-auto mt-10 flex max-w-3xl flex-col items-center gap-6 rounded-3xl border border-grafite/10 bg-bege px-7 py-8 text-center sm:flex-row sm:justify-between sm:px-10 sm:text-left"
             data-aos="fade-up">
            <div class="flex flex-col items-center gap-4 sm:flex-row">
                @include('partials._icon', ['name' => 'google', 'class' => 'h-9 w-9 flex-none'])
                <p class="text-sm leading-relaxed text-grafite-700">{{ $g['texto'] }}</p>
            </div>

            <div class="flex flex-none flex-wrap items-center justify-center gap-3">
                <a href="{{ $g['url'] }}" target="_blank" rel="noopener" class="btn-primario">{{ $g['cta_ver'] }}</a>
                <a href="{{ $g['url'] }}" target="_blank" rel="noopener" class="btn-secundario">{{ $g['cta_deixar'] }}</a>
            </div>
        </div>
    </div>
</section>
@endif
