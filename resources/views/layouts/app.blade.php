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
    @php($agendaCfg = config('institucional.agenda'))
    @php($agendaAbertura = \Illuminate\Support\Carbon::createFromFormat('H:i', $agendaCfg['abertura']))
    @php($agendaFechamento = \Illuminate\Support\Carbon::createFromFormat('H:i', $agendaCfg['fechamento']))
    @php($horarios = collect(range(0, $agendaAbertura->diffInMinutes($agendaFechamento) - 1, (int) $agendaCfg['intervalo_slots']))->map(fn ($m) => $agendaAbertura->copy()->addMinutes($m)->format('H:i'))->values()->all())
    <script>
        window.SIMONE = {
            whatsappNumero: @json(preg_replace('/\D+/', '', config('institucional.marca.whatsapp_publico'))),
            mensagemTemplate: @json(config('institucional.whatsapp.mensagem_agendamento')),
            tipos: @json($agendaCfg['tipos_servico']),
            agenda: {
                min_data: @json(now()->addHours((int) $agendaCfg['antecedencia_minima_horas'])->format('Y-m-d')),
                max_data: @json(now()->addDays((int) $agendaCfg['janela_futura_dias'])->format('Y-m-d')),
                horarios: @json($horarios),
            },
            googleAgenda: {
                marcaNome: @json(config('institucional.marca.nome')),
                regiao: @json(config('institucional.marca.regiao')),
                timezone: @json(config('app.timezone')),
                duracaoPorServico: @json($agendaCfg['duracao_por_servico']),
                duracaoPadrao: @json($agendaCfg['duracao_padrao']),
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
