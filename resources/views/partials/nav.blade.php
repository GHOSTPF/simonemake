@php($marca = config('institucional.marca'))

<header
    x-data="{ aberto: false, opaco: false }"
    x-init="opaco = window.scrollY > 20; window.addEventListener('scroll', () => opaco = window.scrollY > 20)"
    class="fixed inset-x-0 top-0 z-40 transition-colors duration-300 ease-suave"
    :class="opaco || aberto ? 'bg-branco/95 backdrop-blur border-b border-grafite/10' : 'bg-transparent'"
>
    <nav class="container-site flex items-center justify-between py-4" aria-label="Principal">
        <a href="#conteudo" class="group flex items-center gap-3 leading-none" aria-label="{{ $marca['nome'] }} — início">
            @if (!empty($marca['logo']) && file_exists(public_path($marca['logo'])))
                <img src="{{ asset($marca['logo']) }}" alt="{{ $marca['logo_alt'] ?? $marca['nome'] }}"
                     class="h-12 w-auto sm:h-14" width="700" height="560">
            @else
                <span class="flex flex-col">
                    <span class="font-serif text-lg tracking-wide text-grafite">{{ $marca['nome'] }}</span>
                    <span class="mt-0.5 text-[0.65rem] uppercase tracking-kicker text-dourado-700">
                        Noivas · Madrinhas · Festas
                    </span>
                </span>
            @endif
        </a>

        <div class="hidden items-center gap-8 md:flex">
            <a href="#sobre" class="text-sm text-grafite-700 transition hover:text-dourado-700">Sobre</a>
            <a href="#autoridade" class="text-sm text-grafite-700 transition hover:text-dourado-700">Autoridade</a>
            <a href="#territorio" class="text-sm text-grafite-700 transition hover:text-dourado-700">Cuidado</a>
            <a href="#servicos" class="text-sm text-grafite-700 transition hover:text-dourado-700">Serviços</a>
            <a href="#trabalhos" class="text-sm text-grafite-700 transition hover:text-dourado-700">Trabalhos</a>
            <a href="#agendar" class="btn-primario !px-5 !py-2.5">Reserve sua data</a>
        </div>

        <button
            type="button"
            class="md:hidden -mr-2 inline-flex h-10 w-10 items-center justify-center text-grafite"
            :aria-expanded="aberto"
            aria-label="Abrir menu"
            @click="aberto = !aberto"
        >
            <svg x-show="!aberto" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/>
            </svg>
            <svg x-show="aberto" x-cloak class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/>
            </svg>
        </button>
    </nav>

    <div
        x-show="aberto"
        x-cloak
        x-transition.origin.top
        class="md:hidden border-t border-grafite/10 bg-branco"
    >
        <div class="container-site flex flex-col gap-1 py-4">
            @foreach (['#sobre' => 'Sobre', '#autoridade' => 'Autoridade', '#territorio' => 'Cuidado', '#servicos' => 'Serviços', '#trabalhos' => 'Trabalhos'] as $href => $label)
                <a href="{{ $href }}" @click="aberto = false" class="rounded-lg px-2 py-3 text-grafite-700 hover:bg-bege">{{ $label }}</a>
            @endforeach
            <a href="#agendar" @click="aberto = false" class="btn-primario mt-2">Reserve sua data</a>
        </div>
    </div>
</header>
