@php($marca = config('institucional.marca'))
@php($footer = config('institucional.footer'))
@php($waLink = 'https://wa.me/' . preg_replace('/\D+/', '', $marca['whatsapp_publico']) . '?text=' . rawurlencode($marca['whatsapp_texto_padrao']))

<footer class="bg-grafite text-bege">
    <div class="container-site py-16">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2">
                @if (!empty($marca['logo']) && file_exists(public_path($marca['logo'])))
                    {{-- brightness-0 invert => logo em branco sólido, legível sobre o grafite --}}
                    <img src="{{ asset($marca['logo']) }}" alt="{{ $marca['logo_alt'] ?? $marca['nome'] }}"
                         class="h-14 w-auto opacity-90 brightness-0 invert" width="700" height="560">
                @else
                    <p class="font-serif text-xl text-branco">{{ $marca['nome'] }}</p>
                @endif
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-bege/70">{{ $footer['tagline'] }}</p>
            </div>

            <div>
                <p class="text-xs uppercase tracking-kicker text-dourado">Contato</p>
                <ul class="mt-4 space-y-2 text-sm text-bege/80">
                    <li>
                        <a href="{{ $marca['instagram_url'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 hover:text-dourado">
                            @include('partials._icon', ['name' => 'instagram', 'class' => 'h-4 w-4'])
                            {{ $marca['instagram_user'] }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ $waLink }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 hover:text-dourado">
                            @include('partials._icon', ['name' => 'whatsapp', 'class' => 'h-4 w-4'])
                            WhatsApp
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <p class="text-xs uppercase tracking-kicker text-dourado">Atendimento</p>
                <p class="mt-4 inline-flex items-center gap-2 text-sm text-bege/80">
                    @include('partials._icon', ['name' => 'map-pin', 'class' => 'h-4 w-4'])
                    {{ $marca['cidade'] }}
                </p>
            </div>
        </div>

        <div class="mt-14 flex flex-col items-center justify-between gap-3 border-t border-bege/15 pt-6 text-xs text-bege/50 sm:flex-row">
            <p>&copy; {{ date('Y') }} {{ $marca['nome'] }}. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>
