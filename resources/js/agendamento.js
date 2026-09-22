/*
|--------------------------------------------------------------------------
| Componente Alpine — formulário "Reserve sua data"
|--------------------------------------------------------------------------
| Registrado em app.js como Alpine.data('agendamento', ...).
| Uso no Blade:  <div x-data="agendamento" x-init="init()"> ... </div>
|
| Sem backend: ao enviar, o formulário abre duas abas, ambas via link
| público (sem API/OAuth):
|   1. WhatsApp da visitante com a mensagem já preenchida (wa.me);
|   2. Tela de "novo evento" do Google Agenda já preenchida
|      (calendar.google.com/calendar/render?action=TEMPLATE...).
| Em ambos os casos quem confirma o envio/salvamento é a própria visitante.
|
| Config vem de window.SIMONE (injetado no layout Blade):
|   whatsappNumero, mensagemTemplate, tipos (mapa slug->label),
|   agenda (min/max data, horarios), googleAgenda (marcaNome, regiao,
|   timezone, duracaoPorServico, duracaoPadrao)
*/

function pad2(n) {
    return String(n).padStart(2, '0');
}

export default function agendamento() {
    const cfg = window.SIMONE || {};
    const ga = cfg.googleAgenda || {};

    return {
        // ---- campos do formulário ----
        form: {
            nome: '',
            tipo_servico: '',
            data: '',
            hora: '',
        },

        // ---- limites de data e horários (do config PHP) ----
        minData: cfg.agenda?.min_data || '',
        maxData: cfg.agenda?.max_data || '',
        horarios: cfg.agenda?.horarios || [],

        // ---- estado da tela ----
        estado: 'idle', // idle | sucesso
        erros: {}, // { campo: 'mensagem' }
        resumo: null, // dados confirmados p/ tela de sucesso
        linkGoogleAgenda: '', // fallback caso a 2ª aba seja bloqueada pelo navegador

        tipos: cfg.tipos || {},

        init() {},

        selecionarHora(h) {
            this.form.hora = h;
            this.erros.hora = '';
        },

        // -----------------------------------------------------------------
        // Validação
        // -----------------------------------------------------------------
        validar() {
            const e = {};
            if (this.form.nome.trim().length < 2) e.nome = 'Informe seu nome.';
            if (!this.form.tipo_servico) e.tipo_servico = 'Escolha o tipo de serviço.';
            if (!this.form.data) e.data = 'Escolha uma data.';
            if (!this.form.hora) e.hora = 'Escolha um horário.';

            this.erros = e;
            return Object.keys(e).length === 0;
        },

        formatarData(data) {
            return data ? data.split('-').reverse().join('/') : '';
        },

        montarMensagem(dataFmt, servicoLabel) {
            return (cfg.mensagemTemplate || '')
                .replace(':nome', this.form.nome.trim())
                .replace(':servico', servicoLabel)
                .replace(':data', dataFmt)
                .replace(':hora', this.form.hora);
        },

        // -----------------------------------------------------------------
        // Link do Google Agenda (sem API — só a URL pública de novo evento)
        // -----------------------------------------------------------------
        montarLinkGoogleAgenda(servicoLabel) {
            const [ano, mes, dia] = this.form.data.split('-').map(Number);
            const [hora, minuto] = this.form.hora.split(':').map(Number);

            const inicio = new Date(ano, mes - 1, dia, hora, minuto, 0);
            const duracao = ga.duracaoPorServico?.[this.form.tipo_servico] ?? ga.duracaoPadrao ?? 60;
            const fim = new Date(inicio.getTime() + duracao * 60000);

            const fmt = (d) =>
                `${d.getFullYear()}${pad2(d.getMonth() + 1)}${pad2(d.getDate())}` +
                `T${pad2(d.getHours())}${pad2(d.getMinutes())}00`;

            const params = new URLSearchParams({
                action: 'TEMPLATE',
                text: `${ga.marcaNome || ''} — ${servicoLabel} (${this.form.nome.trim()})`,
                dates: `${fmt(inicio)}/${fmt(fim)}`,
                details: `Agendamento feito pelo site.\nServiço: ${servicoLabel}`,
                ctz: ga.timezone || 'America/Sao_Paulo',
            });

            if (ga.regiao) params.set('location', ga.regiao);

            return `https://calendar.google.com/calendar/render?${params.toString()}`;
        },

        // -----------------------------------------------------------------
        // Envio — abre o WhatsApp e o Google Agenda, ambos já preenchidos
        // -----------------------------------------------------------------
        enviar() {
            if (!this.validar()) return;

            const dataFmt = this.formatarData(this.form.data);
            const servicoLabel = this.tipos[this.form.tipo_servico] || this.form.tipo_servico;
            const texto = this.montarMensagem(dataFmt, servicoLabel);
            const urlWhatsApp = `https://wa.me/${cfg.whatsappNumero}?text=${encodeURIComponent(texto)}`;
            const urlGoogleAgenda = this.montarLinkGoogleAgenda(servicoLabel);

            this.resumo = {
                servico: servicoLabel,
                data: dataFmt,
                hora: this.form.hora,
            };
            this.linkGoogleAgenda = urlGoogleAgenda;

            window.open(urlWhatsApp, '_blank', 'noopener');
            window.open(urlGoogleAgenda, '_blank', 'noopener');
            this.estado = 'sucesso';
        },

        recomecar() {
            this.estado = 'idle';
            this.erros = {};
            this.resumo = null;
            this.linkGoogleAgenda = '';
            this.form = { nome: '', tipo_servico: '', data: '', hora: '' };
        },
    };
}
