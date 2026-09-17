<?php

/*
|--------------------------------------------------------------------------
| Conteúdo institucional do site — Simone Gomes (@sigomesmakehair)
|--------------------------------------------------------------------------
|
| TODO O TEXTO DO SITE MORA AQUI. As views (resources/views/partials/*)
| apenas leem estas chaves. Para ajustar qualquer palavra do site,
| edite este arquivo — não é preciso mexer no HTML/Blade.
|
| ATENÇÃO: os textos abaixo são um RASCUNHO INICIAL tirado do briefing.
| Revise com a Simone antes de publicar (especialmente Sobre, Território
| e o exemplo do vestido manchado, que é sensível).
|
| Depois de editar, rode:  php artisan config:clear
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Marca / dados de contato
    |--------------------------------------------------------------------------
    */
    'marca' => [
        'nome'            => 'Simone Gomes',
        'assinatura'      => 'Maquiagem & Penteados · Noivas, Madrinhas e Festas',
        // Logo exibida no topo e no rodapé. Arquivo em public/ (PNG com fundo
        // transparente). Deixe como null para voltar ao nome em texto.
        'logo'            => 'img/logo.png',
        'logo_alt'        => 'SI Gomes — Make & Hair',
        'instagram_user'  => '@sigomesmakehair',
        'instagram_url'   => 'https://instagram.com/sigomesmakehair',
        // Número de WhatsApp EXIBIDO ao público (link "clique e converse").
        // Formato internacional só com dígitos. Ex.: 5583999999999
        'whatsapp_publico'      => '5583999999999',
        'whatsapp_texto_padrao' => 'Olá, Simone! Vim pelo site e gostaria de informações sobre agenda.',
        'cidade'          => 'João Pessoa/PB',
        'regiao'          => 'João Pessoa e região — Paraíba',
        'email'           => 'contato@simonegomes.com.br',
        'agencia'         => 'Agência Virtus MKT',
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO / <head>
    |--------------------------------------------------------------------------
    */
    'seo' => [
        'title'       => 'Simone Gomes — Maquiagem e Penteados para Noivas e Madrinhas | João Pessoa/PB',
        'description' => 'Beleza que resiste a horas de festa, foto, calor e emoção — sem marcar o vestido. '
                       . 'Maquiagem e penteados para noivas, madrinhas e formandas em João Pessoa/PB. '
                       . 'Instrutora do SENAC.',
    ],

    /*
    |--------------------------------------------------------------------------
    | 1. HERO
    |--------------------------------------------------------------------------
    */
    'hero' => [
        'kicker'    => 'João Pessoa/PB · Noivas · Madrinhas · Festas',
        'titulo'    => 'Na festa de outra pessoa, não existe segunda tentativa.',
        'subtitulo' => 'Maquiagem e penteados que atravessam a cerimônia, a foto, o calor e o abraço '
                     . 'apertado — e continuam intactos na hora de jogar o buquê.',
        'cta_texto'      => 'Reserve sua data',
        'cta_secundario' => 'Ver trabalhos',
        // Imagem de fundo do hero (coloque o arquivo em public/img/ e ajuste o nome).
        'imagem'    => 'img/hero.jpg',
        'imagem_alt'=> 'Noiva pronta, retrato em luz natural suave',
    ],

    /*
    |--------------------------------------------------------------------------
    | 2. SOBRE / TRAJETÓRIA
    |--------------------------------------------------------------------------
    | 'resumo'  = texto curto sempre visível.
    | 'completo'= parágrafos revelados no "ler mais" (accordion/modal).
    */
    'sobre' => [
        'titulo'  => 'Quem vai cuidar da sua beleza',
        'resumo'  => 'Simone Gomes começou a trabalhar cedo e passou anos como promotora de vendas. '
                   . 'Depois de um acidente de moto e de uma demissão, transformou o ponto de virada em '
                   . 'profissão: formou-se Cabeleireira Profissional pelo SENAC, especializou-se em mechas '
                   . 'e coloração e passou a dar aula. Hoje é instrutora do SENAC e concentra a agenda em '
                   . 'noivas, madrinhas e formandas de João Pessoa.',
        'completo' => [
            'A rotina de promotora de vendas ensinou o básico que sustenta o trabalho até hoje: '
            . 'chegar antes da hora, entender o que o cliente realmente precisa e não prometer o que '
            . 'não dá para entregar.',

            'O acidente de moto e a demissão que veio depois foram o empurrão para arriscar o que '
            . 'sempre gostou de fazer. O curso no SENAC foi feito sem sobra de recursos, dividindo '
            . 'material e aproveitando cada aula.',

            'A especialização em mechas e coloração abriu a agenda de salão — a base recorrente que '
            . 'paga as contas todo mês. A docência veio em seguida: primeiro uma passagem pelo '
            . 'Instituto Embelleze, depois a vaga de instrutora no SENAC, onde ensina quem está '
            . 'começando agora.',

            'Hoje o foco é evento: noiva, madrinha, mãe de noiva, formanda. O tipo de trabalho em '
            . 'que não há ensaio — e é exatamente por isso que ele exige método.',
        ],
        'ler_mais' => 'Ler a história completa',
        'ler_menos'=> 'Fechar',
    ],

    /*
    |--------------------------------------------------------------------------
    | 3. AUTORIDADE E PROVAS  (cards)
    |--------------------------------------------------------------------------
    | 'icone' = nome de um ícone Heroicons outline (ver partials/_icon.blade.php).
    */
    'autoridade' => [
        'titulo'   => 'Por que confiar o dia na Simone',
        'subtitulo'=> 'Formação reconhecida, sala de aula e trabalho entregue sob pressão real.',
        'cards' => [
            [
                'icone'     => 'academic-cap',
                'titulo'    => 'Instrutora do SENAC',
                'descricao' => 'Formação em Cabeleireiro Profissional pela própria instituição e, '
                             . 'hoje, à frente das turmas — ensinando a técnica que aplica nos eventos.',
            ],
            [
                'icone'     => 'sparkles',
                'titulo'    => 'Passagem pelo Instituto Embelleze',
                'descricao' => 'Experiência como docente em uma das maiores redes de beleza do país, '
                             . 'antes de concentrar a agenda em noivas e madrinhas.',
            ],
            [
                'icone'     => 'book-open',
                'titulo'    => 'Cursos próprios',
                'descricao' => 'Ministra formações autorais para profissionais de maquiagem e '
                             . 'penteado — o que mantém a técnica sempre revisada e atualizada.',
            ],
            [
                'icone'     => 'camera',
                'titulo'    => 'Campanha audiovisual — Governo Lucas Ribeiro',
                'descricao' => 'Assinou a beleza da campanha do candidato ao governo Lucas Ribeiro, '
                             . 'incluindo a primeira-dama e o elenco, sob a pressão de gravação.',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 4. TERRITÓRIO / RESPONSABILIDADE
    |--------------------------------------------------------------------------
    | Seção-diferencial. 'linha' é o texto que a linha dourada revela na
    | animação do GSAP.
    */
    'territorio' => [
        'kicker' => 'O que está por trás de um trabalho que dura',
        'titulo' => 'A escolha da profissional é a diferença entre lembrar da festa e lembrar do reparo',
        'linha'  => 'Segurança antes de estética.',
        'intro'  => 'Noiva e madrinha não têm como refazer. A maquiagem e o penteado precisam durar '
                  . 'horas, aguentar foto com flash, calor de igreja lotada, choro e abraço — e sair '
                  . 'do vestido sem deixar marca. Isso não é sorte: é preparo.',
        'itens' => [
            [
                'titulo' => 'Preparo de pele',
                'texto'  => 'A fixação começa antes da primeira cor: limpeza, hidratação na medida certa '
                          . 'e produtos compatíveis com o tipo de pele e com o clima do dia.',
            ],
            [
                'titulo' => 'Produtos à prova de transferência',
                'texto'  => 'Fórmulas selecionadas para não passar para o vestido, para o véu nem para o '
                          . 'rosto de quem vem abraçar. O branco do vestido é intocável.',
            ],
            [
                'titulo' => 'Teste prévio',
                'texto'  => 'Sempre que possível, um ensaio antes da data para acertar tom, formato e '
                          . 'duração — e chegar no dia sem surpresa.',
            ],
            [
                'titulo' => 'Cronograma do dia',
                'texto'  => 'Ordem e horário de cada etapa combinados com antecedência: quem senta '
                          . 'primeiro, quanto tempo cada pessoa leva, margem para imprevisto.',
            ],
        ],
        // Exemplo sensível — revisar tom com a Simone antes de publicar.
        // Deixe 'exemplo' => null para ocultar completamente esta caixa.
        'exemplo' => [
            'titulo' => 'Um exemplo de por que o cuidado importa',
            'texto'  => 'Já vi de perto um produto errado manchar o vestido de uma noiva a poucos '
                      . 'minutos da cerimônia. Não dá para voltar atrás nesse momento. É por isso que '
                      . 'hoje cada item usado perto do vestido é testado antes — o susto de uma vez '
                      . 'virou protocolo para sempre.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 5. SERVIÇOS / OFERTA
    |--------------------------------------------------------------------------
    | 'slug' precisa bater com as chaves de agenda.tipos_servico abaixo,
    | para a duração do slot ser calculada corretamente.
    */
    'servicos' => [
        'titulo'    => 'Serviços',
        'subtitulo' => 'Agenda aberta para eventos em João Pessoa e região. Reserva por ordem de data.',
        'pacotes' => [
            [
                'slug'      => 'noiva',
                'nome'      => 'Noiva',
                'resumo'    => 'Maquiagem e penteado da noiva, com teste prévio e acompanhamento até '
                             . 'a saída para a cerimônia.',
                'inclui'    => ['Teste prévio agendado', 'Preparo de pele completo', 'Maquiagem + penteado', 'Retoque final antes de sair'],
                'destaque'  => true,
            ],
            [
                'slug'      => 'madrinha',
                'nome'      => 'Madrinhas em grupo',
                'resumo'    => 'Atendimento de madrinhas e mãe da noiva no mesmo dia, com cronograma '
                             . 'organizado para todo mundo ficar pronto sem correria.',
                'inclui'    => ['Cronograma por pessoa', 'Maquiagem + penteado', 'Equipe conforme o tamanho do grupo'],
                'destaque'  => false,
            ],
            [
                'slug'      => 'festa',
                'nome'      => 'Festa & Formatura',
                'resumo'    => 'Convidada de casamento, formanda, aniversariante, mãe de noiva — '
                             . 'beleza para durar a festa inteira.',
                'inclui'    => ['Preparo de pele', 'Maquiagem + penteado', 'Acabamento à prova de foto'],
                'destaque'  => false,
            ],
            [
                'slug'      => 'mechas_coloracao',
                'nome'      => 'Mechas & Coloração',
                'resumo'    => 'Atendimento de salão para mechas, coloração e manutenção de cor, '
                             . 'com diagnóstico do fio antes de qualquer processo químico.',
                'inclui'    => ['Diagnóstico do fio', 'Mechas / coloração', 'Finalização'],
                'destaque'  => false,
            ],
        ],
        // CTA discreto para cursos — a conversão principal do site é agendamento,
        // não venda de curso. Deixe 'cursos' => null para ocultar.
        'cursos' => [
            'texto' => 'Você é profissional de beleza e quer aprender a técnica?',
            'link_texto' => 'Fale sobre os cursos da Simone',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 6. PORTFÓLIO / PROVA VISUAL
    |--------------------------------------------------------------------------
    | Coloque as imagens em public/img/portfolio/ e liste abaixo.
    | 'tipo' é livre (usado só como legenda): 'Antes e depois', 'Bastidor', etc.
    */
    'portfolio' => [
        'titulo'    => 'Trabalhos',
        'subtitulo' => 'Resultado sob luz de festa, bastidores e antes/depois.',
        'imagens' => [
            ['src' => 'img/portfolio/01.jpg', 'alt' => 'Penteado solto com acabamento de festa', 'tipo' => 'Penteado'],
            ['src' => 'img/portfolio/02.jpg', 'alt' => 'Produção completa para ensaio glamouroso', 'tipo' => 'Editorial'],
            ['src' => 'img/portfolio/03.jpg', 'alt' => 'Coque baixo com acabamento dourado', 'tipo' => 'Penteado preso'],
            ['src' => 'img/portfolio/04.jpg', 'alt' => 'Make esfumado com acabamento natural', 'tipo' => 'Retrato'],
            ['src' => 'img/portfolio/05.jpg', 'alt' => 'Make de festa com glitter e olho esfumado', 'tipo' => 'Sob luz de festa'],
            ['src' => 'img/portfolio/06.jpg', 'alt' => 'Sorriso natural em luz de fim de tarde', 'tipo' => 'Luz natural'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 7. DEPOIMENTOS  (carrossel)
    |--------------------------------------------------------------------------
    */
    'depoimentos' => [
        'titulo' => 'O que as clientes contam',
        'itens' => [
            ['texto' => 'Simone tem um excelente atendimento, é uma profissional muito competente, simpática e paciente. Trabalho maravilhoso! Recomendo demais!', 'nome' => 'Amanda Medeiros'],
            ['texto' => 'Um espaço aconchegante ,se você entrar triste, sai super feliz, porque a entrega do serviço vai muito além do esperado: produtos de qualidade, atendimento humanizado e uma profissional que entrega a beleza em dobro às suas clientes. Super confio e indico!', 'nome' => 'Anne Caroline Araújo'],
            ['texto' => 'Uma receptividade nota mil. Profissional muito competente, receptiva, tem segurança no que faz e, acima de tudo, eleva bastante nossa autoestima. Tem muita habilidade em tudo que faz!', 'nome' => 'Aparecida Isidro'],
            ['texto' => 'Já cuida do meu cabelo há anos, profissional impecável e completa!', 'nome' => 'Niomara Andrade'],
            ['texto' => 'Amo o atendimento e o serviço. Muito bem atendida. Super satisfeita.', 'nome' => 'Janine Morais'],
            ['texto' => 'Atendimento excelente, ótima profissional, recomendo.', 'nome' => 'Joice Kamaique Almeida Dodo'],
            ['texto' => 'Tudo fica perfeito por ela!', 'nome' => 'Ivana Geane'],
            ['texto' => 'Top demais, super indico!', 'nome' => 'Marília Oliveira'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 8. PESSOAL (nota humana — tratar com discrição)
    |--------------------------------------------------------------------------
    | Sem nomes/idades das filhas em destaque. Deixe 'pessoal' => null p/ ocultar.
    */
    'pessoal' => [
        'texto' => 'Fora do trabalho, Simone é casada há 18 anos e mãe de três filhas. Divide, como '
                 . 'a maioria das clientes, a rotina entre casa, profissão e o cuidado com a própria '
                 . 'autoestima — e é desse lugar que ela entende o que um dia de festa representa.',
    ],

    /*
    |--------------------------------------------------------------------------
    | 9. SEÇÃO DE CONTATO / AGENDAMENTO
    |--------------------------------------------------------------------------
    */
    'contato' => [
        'kicker' => 'Agenda aberta',
        'titulo' => 'Reserve sua data',
        'subtitulo' => 'Escolha o dia e o horário disponíveis. A Simone recebe o pedido na hora e '
                     . 'confirma os detalhes com você pelo WhatsApp.',
        'obs_form' => 'Ao enviar, você reserva provisoriamente o horário. A confirmação final e o '
                    . 'valor são combinados no atendimento.',
        'sucesso_titulo' => 'Pedido recebido!',
        'sucesso_texto'  => 'A Simone já foi avisada e retorna pelo WhatsApp para confirmar. '
                          . 'Seu horário está reservado.',
        'erro_conflito'  => 'Esse horário acabou de ser reservado. Escolha outro, por favor.',
        'erro_generico'  => 'Não foi possível enviar agora. Tente novamente em instantes ou chame '
                          . 'no WhatsApp.',
    ],

    /*
    |--------------------------------------------------------------------------
    | 10. FOOTER
    |--------------------------------------------------------------------------
    */
    'footer' => [
        'tagline'   => 'Beleza para quem não pode contar com uma segunda tentativa.',
        // 'assinatura'=> 'Feito por Agência Virtus MKT',
    ],

    /*
    |--------------------------------------------------------------------------
    | AGENDA — regras de disponibilidade  (AJUSTE AQUI)
    |--------------------------------------------------------------------------
    |
    | Estas configurações controlam quais horários aparecem no formulário.
    |
    */
    'agenda' => [

        // Dias da semana em que a Simone atende.
        // 1 = segunda ... 7 = domingo (padrão ISO-8601 / Carbon::dayOfWeekIso).
        'dias_atendimento' => [2, 3, 4, 5, 6], // terça a sábado

        // Janela de funcionamento (horário local). Formato "HH:MM".
        'abertura'    => '08:00',
        'fechamento'  => '18:00',

        // De quanto em quanto tempo um novo horário pode começar (em minutos).
        // Ex.: 60 => slots começam 08:00, 09:00, 10:00...
        'intervalo_slots' => 60,

        // Antecedência mínima para reservar (em horas). Bloqueia "hoje daqui a pouco".
        'antecedencia_minima_horas' => 24,

        // Até quantos dias no futuro a agenda fica aberta.
        'janela_futura_dias' => 120,

        // Duração de cada tipo de serviço (em minutos). A 'chave' precisa ser
        // igual ao 'slug' do pacote em 'servicos.pacotes' e ao value do <select>
        // no formulário. Um serviço mais longo ocupa mais horários seguidos.
        'duracao_por_servico' => [
            'noiva'            => 180,
            'madrinha'         => 90,
            'festa'            => 60,
            'mechas_coloracao' => 120,
            'outro'            => 60,
        ],

        // Duração usada quando o tipo não está no mapa acima.
        'duracao_padrao' => 60,

        // Opções exibidas no <select> "tipo de serviço" do formulário.
        // O 'value' precisa existir em 'duracao_por_servico'.
        'tipos_servico' => [
            'noiva'            => 'Noiva',
            'madrinha'         => 'Madrinha',
            'festa'            => 'Festa / Formatura',
            'mechas_coloracao' => 'Mechas e coloração',
            'outro'            => 'Outro',
        ],

        // Datas bloqueadas manualmente (férias, feriados, compromissos).
        // Formato "YYYY-MM-DD".
        'bloqueios' => [
            // '2026-12-25',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | GOOGLE AGENDA — como o evento é montado
    |--------------------------------------------------------------------------
    */
    'google_agenda' => [
        // Prefixo do título do evento criado na agenda da Simone.
        'prefixo_titulo' => '[Site] ',
        // Fuso usado para montar início/fim do evento.
        'timezone' => 'America/Recife',
    ],

    /*
    |--------------------------------------------------------------------------
    | WHATSAPP — mensagem enviada para a Simone
    |--------------------------------------------------------------------------
    |
    | Placeholders disponíveis: :nome :telefone :servico :data :hora :observacao
    |
    */
    'whatsapp' => [
        'mensagem_simone' => "*Novo agendamento pelo site*\n\n"
            . "Cliente: :nome\n"
            . "WhatsApp: :telefone\n"
            . "Serviço: :servico\n"
            . "Data: :data\n"
            . "Horário: :hora\n"
            . "Obs.: :observacao",

        // Confirmação automática para a CLIENTE.
        // A Meta só permite iniciar conversa fora da janela de 24h via TEMPLATE
        // pré-aprovado. Deixe 'template_cliente' => null enquanto não houver um
        // template aprovado — nesse caso a cliente não recebe mensagem automática.
        // Quando tiver o template aprovado, preencha o nome e o idioma abaixo.
        'template_cliente' => [
            'nome'   => null,          // ex.: 'confirmacao_agendamento'
            'idioma' => 'pt_BR',
            // Ordem dos parâmetros do corpo do template ({{1}}, {{2}}, ...).
            'parametros' => ['nome', 'data', 'hora'],
        ],
    ],
];
