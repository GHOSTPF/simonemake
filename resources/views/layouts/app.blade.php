<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php($seo = config('institucional.seo'))
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">

    {{-- Open Graph básico --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:locale" content="pt_BR">
    @if (file_exists(public_path('img/og.jpg')))
        <meta property="og:image" content="{{ asset('img/og.jpg') }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Inter:wght@300;400;500;600&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Config lida pelo componente Alpine do formulário (resources/js/agendamento.js) --}}
    <script>
        window.SIMONE = {
            csrf: @json(csrf_token()),
            endpoints: {
                disponibilidade: @json(route('disponibilidade')),
                agendar: @json(route('agendar')),
            },
            tipos: @json(config('institucional.agenda.tipos_servico')),
            textos: {
                erro_conflito: @json(config('institucional.contato.erro_conflito')),
                erro_generico: @json(config('institucional.contato.erro_generico')),
            },
            agenda: {
                min_data: @json(now()->addHours((int) config('institucional.agenda.antecedencia_minima_horas'))->format('Y-m-d')),
                max_data: @json(now()->addDays((int) config('institucional.agenda.janela_futura_dias'))->format('Y-m-d')),
            },
        };
    </script>
</head>
<body class="bg-branco text-grafite">
    <a href="#conteudo"
       class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-grafite focus:px-4 focus:py-2 focus:text-branco">
        Pular para o conteúdo
    </a>

    @include('partials.nav')

    <main id="conteudo">
        @yield('conteudo')
    </main>

    @include('partials.footer')
</body>
</html>
