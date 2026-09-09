@php($pessoal = config('institucional.pessoal'))
@if (!empty($pessoal))
<section class="bg-branco py-16">
    <div class="container-site">
        <div class="mx-auto max-w-prosa border-t border-dourado/40 pt-10 text-center" data-aos="fade-up">
            <p class="text-sm italic leading-relaxed text-grafite-500">{{ $pessoal['texto'] }}</p>
        </div>
    </div>
</section>
@endif
