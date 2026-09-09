# Site Simone Gomes — Portfólio + Agendamento

Site institucional (página única) da maquiadora e penteadista **Simone Gomes**
(@sigomesmakehair — João Pessoa/PB), com formulário de **contato/agendamento**
que:

1. checa se a data + hora escolhida está livre;
2. grava o agendamento (com proteção contra condição de corrida);
3. avisa a Simone no **WhatsApp** (Cloud API da Meta) — via fila;
4. cria o evento correspondente na **Google Agenda** dela — via fila.

Site público: **sem login, sem painel administrativo**.

---

## Stack

| Camada | Tecnologia |
|---|---|
| Backend | Laravel 12 · PHP 8.2+ |
| Banco | PostgreSQL (produção) · SQLite (dev) |
| Views | Blade |
| CSS | Tailwind CSS 3 (paleta da marca em `tailwind.config.js`) |
| Interações | Alpine.js 3 |
| Animação | GSAP + ScrollTrigger (hero + linha dourada), AOS (reveals), Lenis (smooth scroll), Swiper (carrosséis) |
| Build | Vite |
| Google Agenda | `spatie/laravel-google-calendar` (Service Account) |
| WhatsApp | WhatsApp Cloud API (Meta) via HTTP Client nativo |
| Fila | `database` (padrão) — precisa de `php artisan queue:work` |

---

## Estrutura de referência

```
app/
  Http/Controllers/AgendamentoController.php  store() + disponibilidade()
  Http/Requests/AgendarRequest.php            validação server-side
  Jobs/EnviarNotificacaoWhatsApp.php          notificação (fila, afterCommit)
  Jobs/CriarEventoGoogleAgenda.php            evento no Google (fila, afterCommit)
  Models/Agendamento.php
  Services/DisponibilidadeService.php         cálculo de horários livres
  Services/WhatsAppService.php
  Services/GoogleCalendarService.php
  Exceptions/HorarioIndisponivelException.php

config/
  institucional.php   >>> TODO O TEXTO DO SITE + regras de agenda <<<
  google-calendar.php  config do pacote spatie (Service Account)
  services.php         credenciais WhatsApp + Google (lidas do .env)

database/migrations/*_create_agendamentos_table.php   unique(data, hora)

resources/
  views/home.blade.php + views/partials/*.blade.php
  css/app.css
  js/app.js            entry: Alpine, Lenis, AOS
  js/animations.js      GSAP + Swiper
  js/agendamento.js     componente Alpine do formulário

routes/web.php          GET / · GET /disponibilidade · POST /agendar
tailwind.config.js      paleta da marca (branco, bege, areia, dourado, grafite)
```

---

## Setup local rápido (SQLite — sem servidor de banco)

Pré-requisitos: PHP 8.2+, Composer, Node 18+.

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

# .env.example vem configurado para PostgreSQL. Para rodar local com SQLite,
# no .env troque a linha do banco por:
#   DB_CONNECTION=sqlite
# (comente/remova DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD)
touch database/database.sqlite

php artisan migrate
npm run build          # ou: npm run dev  (com hot reload)

# 2 terminais:
php artisan serve                       # http://127.0.0.1:8000
php artisan queue:work                   # processa WhatsApp + Google Agenda
```

Sem as credenciais de Google/WhatsApp o site funciona normalmente: o
agendamento é salvo e os Jobs apenas **logam um aviso** (`storage/logs/laravel.log`)
sem falhar. O agendamento no banco é a fonte de verdade.

---

## Setup de produção (PostgreSQL)

1. Crie o banco e o usuário no Postgres.
2. No `.env`:

   ```
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=simone
   DB_USERNAME=simone
   DB_PASSWORD=********
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://simonegomes.com.br
   ```

3. Deploy:

   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   php artisan migrate --force
   php artisan config:cache && php artisan route:cache && php artisan view:cache
   ```

4. Rode o worker de fila como serviço (systemd/supervisor):

   ```
   php artisan queue:work --tries=3 --timeout=60
   ```

   Exemplo supervisor:

   ```ini
   [program:simone-queue]
   command=php /var/www/simone/artisan queue:work --sleep=3 --tries=3 --timeout=60
   autostart=true
   autorestart=true
   user=www-data
   numprocs=1
   redirect_stderr=true
   stdout_logfile=/var/www/simone/storage/logs/queue.log
   ```

> **Observação sobre `google/apiclient-services`:** esse pacote traz centenas de
> APIs do Google. O `composer.json` já limita a instalação ao serviço
> **Calendar** (`extra."google/apiclient-services": ["Calendar"]` +
> script `Google\Task\Composer::cleanup`), o que reduz muito o tamanho de
> `vendor/`. Isso roda sozinho a cada `composer install`/`update`.

---

## Configurar a Google Agenda (Service Account)

A agenda é **sempre a da Simone** — não há login de cliente.

1. **Google Cloud Console** → crie um projeto.
2. **APIs & Services → Library** → ative **Google Calendar API**.
3. **APIs & Services → Credentials → Create credentials → Service account**.
   Dê um nome (ex.: `agenda-site`), crie.
4. Na conta de serviço criada → aba **Keys → Add key → Create new key → JSON**.
   Baixe o arquivo.
5. Coloque o JSON no servidor **fora do controle de versão**, ex.:
   `storage/app/google-calendar/service-account.json`
   (essa pasta já está no `.gitignore`).
6. No `.env`:

   ```
   GOOGLE_CALENDAR_SERVICE_ACCOUNT_EMAIL=agenda-site@SEU-PROJETO.iam.gserviceaccount.com
   GOOGLE_CALENDAR_SERVICE_ACCOUNT_KEY_PATH=/caminho/absoluto/storage/app/google-calendar/service-account.json
   GOOGLE_CALENDAR_ID=xxxxxxxxxxxxxxxxxxxx@group.calendar.google.com
   ```

7. **Compartilhe a agenda da Simone com a conta de serviço:**
   Google Calendar → passe o mouse na agenda dela → **⋮ → Configurações e
   compartilhamento → Compartilhar com pessoas e grupos específicos** →
   adicione o e-mail `...iam.gserviceaccount.com` com permissão
   **"Fazer alterações nos eventos"**.
8. O **ID da agenda** está em *Configurações da agenda → Integrar agenda → ID da agenda*.
   (Para a agenda principal, o ID costuma ser o próprio e-mail da Simone.)

Teste rápido:

```bash
php artisan tinker
>>> app(App\Services\GoogleCalendarService::class)->configurado()   // true
```

Duração do evento por tipo de serviço: `config/institucional.php` →
`agenda.duracao_por_servico`.

---

## Configurar o WhatsApp Cloud API (Meta)

1. **developers.facebook.com** → crie um App do tipo **Business**.
2. Adicione o produto **WhatsApp**.
3. Em *WhatsApp → API Setup* você verá:
   - **Phone number ID** (ID do número remetente) → `WHATSAPP_PHONE_NUMBER_ID`
   - um **token temporário** (24h). Para produção, gere um **token permanente**
     via *Business Settings → System users* (usuário de sistema com o app
     atribuído e permissão `whatsapp_business_messaging`) → `WHATSAPP_CLOUD_API_TOKEN`
4. `WHATSAPP_NOTIFY_NUMBER` = telefone da Simone que **recebe** o aviso,
   só dígitos e com DDI. Ex.: `5583999999999`.
   Esse número precisa ter iniciado conversa com o número do WhatsApp Business
   pelo menos uma vez (janela de 24h) para receber mensagem de texto simples.

```
WHATSAPP_CLOUD_API_TOKEN=EAAG...
WHATSAPP_PHONE_NUMBER_ID=123456789012345
WHATSAPP_NOTIFY_NUMBER=5583999999999
```

Texto da mensagem enviada à Simone: `config/institucional.php` →
`whatsapp.mensagem_simone` (placeholders `:nome :telefone :servico :data :hora :observacao`).

### Confirmação automática para a CLIENTE (opcional)

Para enviar mensagem para a cliente **fora da janela de 24h**, a Meta exige um
**template pré-aprovado**. Enquanto não houver um template aprovado, a cliente
**não recebe** mensagem automática (por design — só a Simone é avisada).

Quando tiver o template aprovado (ex.: `confirmacao_agendamento` com corpo
`Olá {{1}}, seu horário em {{2}} às {{3}} está reservado...`), preencha em
`config/institucional.php`:

```php
'template_cliente' => [
    'nome'   => 'confirmacao_agendamento',
    'idioma' => 'pt_BR',
    'parametros' => ['nome', 'data', 'hora'], // ordem de {{1}}, {{2}}, {{3}}
],
```

---

## Editar o conteúdo do site

**Tudo em `config/institucional.php`.** As views só leem esse arquivo.
Depois de editar: `php artisan config:clear` (e `config:cache` em produção).

Blocos principais:

| Chave | O que controla |
|---|---|
| `marca` | nome, Instagram, WhatsApp público, cidade |
| `seo` | `<title>` e meta description |
| `hero` | chamada principal, subtítulo, CTAs, imagem de fundo |
| `sobre` | resumo + parágrafos do "ler mais" (accordion) |
| `autoridade.cards` | SENAC / Embelleze / cursos / campanha |
| `territorio` | seção de responsabilidade + a linha "Segurança antes de estética" + caixa do exemplo sensível (`exemplo => null` oculta) |
| `servicos.pacotes` | noiva / madrinha / festa / mechas — `slug` bate com a agenda |
| `servicos.cursos` | CTA discreto de cursos (`null` oculta) |
| `portfolio.imagens` | lista da galeria (Swiper + lightbox) |
| `depoimentos` | carrossel de depoimentos |
| `pessoal` | nota humana sobre família (`null` oculta) |
| `contato` | textos do formulário + mensagens de erro/sucesso |
| `footer` | tagline + assinatura da agência |

> Os textos entregues são um **rascunho inicial** tirado do briefing. Revisar
> com a Simone antes de publicar — em especial *Sobre*, *Território* e o
> exemplo do vestido manchado.

### Regras da agenda (`config/institucional.php` → `agenda`)

| Chave | Padrão | Significado |
|---|---|---|
| `dias_atendimento` | `[2,3,4,5,6]` | dias da semana (1=seg … 7=dom) |
| `abertura` / `fechamento` | `08:00` / `18:00` | janela de funcionamento |
| `intervalo_slots` | `60` | de quantos em quantos minutos um horário pode começar |
| `antecedencia_minima_horas` | `24` | bloqueia reservas "para já" |
| `janela_futura_dias` | `120` | até quando a agenda fica aberta |
| `duracao_por_servico` | noiva 180, madrinha 90, festa 60, mechas 120 | minutos que cada serviço ocupa |
| `bloqueios` | `[]` | datas `YYYY-MM-DD` indisponíveis (férias/feriados) |

### Paleta / identidade visual

`tailwind.config.js` → `theme.extend.colors`. Edite os hex ali para mudar as
cores do site inteiro. Fontes da marca (Cormorant Garamond + Inter) são
carregadas por `<link>` em `resources/views/layouts/app.blade.php`.

### Imagens

Coloque os arquivos em `public/img/` (ver `public/img/LEIA-ME.txt`):
`hero.jpg`, `og.jpg`, `portfolio/01.jpg …`. Enquanto não existirem, o site
mostra **placeholders sóbrios** automaticamente, sem quebrar o layout.

---

## Como funciona a verificação de disponibilidade

- `GET /disponibilidade?data=YYYY-MM-DD&tipo_servico=noiva` → JSON `{ slots: [...] }`.
  O front (Alpine) chama esse endpoint antes de mostrar os horários — a
  visitante **nunca vê um horário ocupado**.
- `DisponibilidadeService` gera os slots a partir da config e subtrai os
  intervalos já ocupados (considerando a **duração de cada serviço**, então
  uma noiva de 3h bloqueia mais horários que uma festa de 1h).
- `POST /agendar`:
  1. valida (`AgendarRequest`);
  2. `DB::transaction()` + `lockForUpdate()` na checagem `data`+`hora`;
  3. `INSERT` — a constraint `unique(['data','hora'])` é a rede de segurança
     final contra corrida (Postgres `23505` → HTTP **409**);
  4. **após o commit**, despacha os dois Jobs na fila (`->afterCommit()`).
- Respostas: `201` agendado · `409` horário ocupado · `422` validação.

Falha de WhatsApp ou Google **não** desfaz o agendamento — o erro é logado
(`Log::error`) e o fluxo segue.

---

## Acessibilidade / animação

- Respeita `prefers-reduced-motion`: com a preferência ativa, Lenis e GSAP são
  desligados e o AOS fica inerte.
- Rede de segurança: se alguma animação não completar, um timeout revela o
  conteúdo — nada fica preso invisível.
- `body { overflow-x: clip }` trava rolagem horizontal acidental.

---

## Testar o fluxo completo localmente

```bash
php artisan serve
php artisan queue:work

# em outro terminal:
curl "http://127.0.0.1:8000/disponibilidade?data=2026-09-23&tipo_servico=noiva"

# reservar (pegue o CSRF do <meta name="csrf-token"> em GET /):
curl -X POST http://127.0.0.1:8000/agendar \
  -H "Content-Type: application/json" -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: <token>" -b <cookies> \
  -d '{"nome":"Maria","telefone":"83999998888","data":"2026-09-23","hora":"09:00","tipo_servico":"noiva"}'
```

Enviar de novo a mesma `data`+`hora` → `409`. Ver os Jobs:
`php artisan queue:work --stop-when-empty` e `storage/logs/laravel.log`.

---

_Feito para Agência Virtus MKT._
