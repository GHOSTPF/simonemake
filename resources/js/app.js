import './bootstrap';

import Alpine from 'alpinejs';
import Lenis from 'lenis';
import AOS from 'aos';

import { initAnimations } from './animations';
import agendamento from './agendamento';

/*
|--------------------------------------------------------------------------
| Preferência de movimento reduzido
|--------------------------------------------------------------------------
| Se o usuário pediu menos animação no SO, desligamos Lenis + GSAP e
| deixamos o AOS praticamente inerte (CSS já zera os reveals).
*/
const semMovimento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Marca o <html> para o CSS poder esconder o estado inicial das animações
document.documentElement.classList.add('js');

/*
|--------------------------------------------------------------------------
| Lenis — smooth scroll global (uma única instância)
|--------------------------------------------------------------------------
*/
let lenis = null;

if (!semMovimento) {
    lenis = new Lenis({
        duration: 1.1,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        smoothWheel: true,
    });

    const raf = (time) => {
        lenis.raf(time);
        requestAnimationFrame(raf);
    };
    requestAnimationFrame(raf);
}

// Exposto para o resto do app (ex.: âncoras do menu, animations.js)
window.lenis = lenis;

// Rolagem suave para âncoras internas (#secao), respeitando Lenis
document.addEventListener('click', (e) => {
    const link = e.target.closest('a[href^="#"]');
    if (!link) return;

    const id = link.getAttribute('href');
    if (id.length < 2) return;

    const alvo = document.querySelector(id);
    if (!alvo) return;

    e.preventDefault();
    if (lenis) {
        lenis.scrollTo(alvo, { offset: -80 });
    } else {
        alvo.scrollIntoView({ behavior: semMovimento ? 'auto' : 'smooth' });
    }
});

/*
|--------------------------------------------------------------------------
| AOS — reveals simples (seções sem coreografia própria)
|--------------------------------------------------------------------------
*/
AOS.init({
    duration: semMovimento ? 0 : 650,
    easing: 'ease-out-cubic',
    once: true,
    offset: 80,
    anchorPlacement: 'top-bottom',
    disable: semMovimento,
});

// Recalcula posições quando fontes/imagens terminam de carregar e sempre
// que o Lenis rola (mantém os gatilhos precisos com o scroll suave).
window.addEventListener('load', () => AOS.refresh());
if (lenis) lenis.on('scroll', () => AOS.refresh());

// Rede de segurança: se por qualquer motivo (scroll programático fora do
// Lenis, etc.) um elemento visível não tiver animado, revela após 4s.
// Nunca deixa conteúdo preso invisível.
if (!semMovimento) {
    setTimeout(() => {
        document.querySelectorAll('[data-aos]:not(.aos-animate)').forEach((el) => {
            const r = el.getBoundingClientRect();
            if (r.top < window.innerHeight && r.bottom > 0) el.classList.add('aos-animate');
        });
    }, 4000);
}

/*
|--------------------------------------------------------------------------
| GSAP + ScrollTrigger + Swiper (arquivo separado)
|--------------------------------------------------------------------------
*/
initAnimations({ reducedMotion: semMovimento, lenis });

/*
|--------------------------------------------------------------------------
| Alpine — componente do formulário de agendamento
|--------------------------------------------------------------------------
*/
Alpine.data('agendamento', agendamento);
window.Alpine = Alpine;
Alpine.start();
