/*
|--------------------------------------------------------------------------
| Componente Alpine — formulário "Reserve sua data"
|--------------------------------------------------------------------------
| Registrado em app.js como Alpine.data('agendamento', ...).
| Uso no Blade:  <div x-data="agendamento" x-init="init()"> ... </div>
|
| Config vem de window.SIMONE (injetado no layout Blade):
|   endpoints.disponibilidade, endpoints.agendar, csrf,
|   tipos (mapa slug->label), textos (sucesso/erro), agenda (min/max data)
*/

export default function agendamento() {
    const cfg = window.SIMONE || {};

    return {
        // ---- campos do formulário ----
        form: {
            nome: '',
            telefone: '',
            data: '',
            hora: '',
            tipo_servico: '',
            observacao: '',
        },

        // ---- limites de data (do config PHP) ----
        minData: cfg.agenda?.min_data || '',
        maxData: cfg.agenda?.max_data || '',

        // ---- estado do seletor de horário ----
        slots: [],
        carregandoSlots: false,
        slotsErro: '',
        jaBuscou: false,

        // ---- estado do envio ----
        estado: 'idle', // idle | enviando | sucesso | erro
        erroGeral: '',
        erros: {}, // { campo: 'mensagem' }
        resumo: null, // dados confirmados p/ tela de sucesso

        tipos: cfg.tipos || {},

        init() {
            // Rebusca horários quando muda a data ou o tipo de serviço
            // (a duração do serviço muda quais horários "cabem").
            this.$watch('form.data', () => this.buscarSlots());
            this.$watch('form.tipo_servico', () => {
                if (this.form.data) this.buscarSlots();
            });
        },

        // -----------------------------------------------------------------
        // Disponibilidade
        // -----------------------------------------------------------------
        async buscarSlots() {
            this.form.hora = '';
            this.slots = [];
            this.slotsErro = '';
            this.erros.hora = '';

            if (!this.form.data) {
                this.jaBuscou = false;
                return;
            }

            this.carregandoSlots = true;
            this.jaBuscou = true;

            try {
                const url = new URL(cfg.endpoints.disponibilidade, window.location.origin);
                url.searchParams.set('data', this.form.data);
                if (this.form.tipo_servico) {
                    url.searchParams.set('tipo_servico', this.form.tipo_servico);
                }

                const resp = await fetch(url, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });

                if (!resp.ok) throw new Error('falha');

                const json = await resp.json();
                this.slots = Array.isArray(json.slots) ? json.slots : [];

                if (this.slots.length === 0) {
                    this.slotsErro = 'Não há horários livres nesse dia. Tente outra data.';
                }
            } catch (e) {
                this.slotsErro = 'Não foi possível carregar os horários. Tente novamente.';
            } finally {
                this.carregandoSlots = false;
            }
        },

        selecionarHora(h) {
            this.form.hora = h;
            this.erros.hora = '';
        },

        // -----------------------------------------------------------------
        // Validação client-side (básica — a que conta é a do servidor)
        // -----------------------------------------------------------------
        validar() {
            const e = {};
            if (this.form.nome.trim().length < 2) e.nome = 'Informe seu nome.';

            const tel = this.form.telefone.replace(/\D+/g, '');
            if (tel.length < 10 || tel.length > 13) e.telefone = 'Informe um WhatsApp válido com DDD.';

            if (!this.form.tipo_servico) e.tipo_servico = 'Escolha o tipo de serviço.';
            if (!this.form.data) e.data = 'Escolha uma data.';
            if (!this.form.hora) e.hora = 'Escolha um horário.';

            this.erros = e;
            return Object.keys(e).length === 0;
        },

        // -----------------------------------------------------------------
        // Envio
        // -----------------------------------------------------------------
        async enviar() {
            this.erroGeral = '';

            if (!this.validar()) return;

            this.estado = 'enviando';

            try {
                const resp = await fetch(cfg.endpoints.agendar, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': cfg.csrf,
                    },
                    body: JSON.stringify({ ...this.form, telefone: this.form.telefone.replace(/\D+/g, '') }),
                });

                if (resp.status === 201) {
                    const json = await resp.json();
                    this.resumo = json.agendamento || null;
                    this.estado = 'sucesso';
                    return;
                }

                if (resp.status === 409) {
                    // Horário ocupado entre a checagem e o envio.
                    const json = await resp.json().catch(() => ({}));
                    this.estado = 'erro';
                    this.erroGeral = json.message || cfg.textos?.erro_conflito || 'Esse horário não está mais disponível.';
                    // Atualiza a lista para a visitante escolher outro.
                    await this.buscarSlots();
                    return;
                }

                if (resp.status === 422) {
                    const json = await resp.json();
                    const errs = {};
                    Object.entries(json.errors || {}).forEach(([campo, msgs]) => {
                        errs[campo] = Array.isArray(msgs) ? msgs[0] : String(msgs);
                    });
                    this.erros = errs;
                    this.estado = 'idle';
                    this.erroGeral = 'Confira os campos destacados.';
                    return;
                }

                throw new Error('status ' + resp.status);
            } catch (e) {
                this.estado = 'erro';
                this.erroGeral = cfg.textos?.erro_generico || 'Não foi possível enviar agora. Tente novamente em instantes.';
            }
        },

        recomecar() {
            this.estado = 'idle';
            this.erroGeral = '';
            this.erros = {};
            this.form.hora = '';
            this.buscarSlots();
        },
    };
}
