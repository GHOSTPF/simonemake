@php($t = config('institucional.territorio'))

<section id="territorio" class="relative overflow-hidden bg-grafite py-24 text-bege sm:py-32">
    <div class="container-site">
        <div class="grid gap-14 lg:grid-cols-12">
            <div class="lg:col-span-5" data-aos="fade-up">
                <p class="text-xs font-medium uppercase tracking-kicker text-dourado">{{ $t['kicker'] }}</p>
                <h2 class="mt-5 font-serif text-3xl leading-tight text-branco sm:text-4xl">
                    {{ $t['titulo'] }}
                </h2>

                {{-- A linha dourada revelada pelo GSAP (data-anim="linha") --}}
                <div class="mt-8 flex items-center gap-4">
                    <span data-anim="linha" class="block h-px w-40 bg-dourado"></span>
                    <span class="font-serif text-lg italic text-dourado">{{ $t['linha'] }}</span>
                </div>

                <p class="mt-8 max-w-prosa leading-relaxed text-bege/80">{{ $t['intro'] }}</p>
            </div>

            <div class="lg:col-span-7">
                <dl data-aos="fade-up" class="grid gap-px overflow-hidden rounded-2xl border border-bege/15 bg-bege/10 sm:grid-cols-2">
                    @foreach ($t['itens'] as $item)
                        <div class="bg-grafite p-7">
                            <dt class="flex items-center gap-3 font-serif text-lg text-branco">
                                <span class="text-dourado">
                                    @include('partials._icon', ['name' => 'shield-check', 'class' => 'h-5 w-5'])
                                </span>
                                {{ $item['titulo'] }}
                            </dt>
                            <dd class="mt-3 text-sm leading-relaxed text-bege/75">{{ $item['texto'] }}</dd>
                        </div>
                    @endforeach
                </dl>

                @if (!empty($t['exemplo']))
                    <div data-aos="fade-up" class="mt-6 rounded-2xl border border-dourado/30 bg-dourado/10 p-7">
                        <p class="font-serif text-lg text-dourado">{{ $t['exemplo']['titulo'] }}</p>
                        <p class="mt-3 text-sm leading-relaxed text-bege/80">{{ $t['exemplo']['texto'] }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
