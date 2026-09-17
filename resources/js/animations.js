/*
|--------------------------------------------------------------------------
| Animações — GSAP + ScrollTrigger + Swiper
|--------------------------------------------------------------------------
| Divisão de responsabilidades:
|   - AOS  (app.js) .... reveals simples ao rolar ([data-aos]). CSS-based,
|                        não depende de requestAnimationFrame — não "trava".
|   - GSAP (aqui) ...... só a coreografia que o AOS não faz bem:
|                        a timeline do hero e a linha dourada do território.
|
| Tom: sofisticação discreta. Transições suaves e curtas, sem exagero.
| Toda animação GSAP tem estado final explícito + rede de segurança, para
| nunca deixar conteúdo preso invisível.
|
| Este arquivo NÃO conhece a lógica do formulário (isso é agendamento.js).
*/

import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Swiper from 'swiper';
import { Navigation, Pagination, A11y, Autoplay } from 'swiper/modules';

gsap.registerPlugin(ScrollTrigger);

// Exposto para debug e para ScrollTrigger.refresh() a partir de componentes
// Alpine (ex.: uma seção re-renderizada — ver secao-contato / portfolio).
window.gsap = gsap;
window.ScrollTrigger = ScrollTrigger;

/**
 * @param {{ reducedMotion: boolean, lenis: import('lenis').default|null }} opts
 */
export function initAnimations({ reducedMotion, lenis }) {
    // Carrosséis funcionam mesmo com movimento reduzido (só sem autoplay).
    initCarrosseis(reducedMotion);

    if (reducedMotion) {
        revelarGsap();
        return;
    }

    // ScrollTrigger acompanha o scroll suave do Lenis. (O loop de rAF do Lenis
    // vive no app.js; aqui só pedimos o update do ScrollTrigger a cada scroll.)
    if (lenis) {
        lenis.on('scroll', ScrollTrigger.update);
    }

    initHero();
    initLinhaDourada();

    // Recalcula gatilhos quando fontes/imagens assentam.
    window.addEventListener('load', () => ScrollTrigger.refresh());

    // Rede de segurança: se algo impedir as timelines de completar,
    // garante o estado final alguns segundos depois.
    setTimeout(revelarGsap, 4000);
}

/* Coloca todos os alvos GSAP no estado final (reduced-motion ou fallback). */
function revelarGsap() {
    gsap.set(
        '[data-hero-media], [data-hero-kicker], [data-hero-titulo] .linha, [data-hero-sub], [data-hero-cta]',
        { opacity: 1, y: 0, scale: 1, yPercent: 0, clearProps: 'transform' }
    );
    gsap.set('[data-anim="linha"]', { scaleX: 1 });
}

/* ---------- Hero: entrada coordenada ---------- */
function initHero() {
    const hero = document.querySelector('[data-hero]');
    if (!hero) return;

    const tl = gsap.timeline({ defaults: { ease: 'power3.out', duration: 0.9 } });

    tl.fromTo('[data-hero-media]', { scale: 1.06, opacity: 0 }, { scale: 1, opacity: 1, duration: 1.4, ease: 'power2.out' }, 0)
        .fromTo('[data-hero-kicker]', { y: 14, opacity: 0 }, { y: 0, opacity: 1 }, 0.15)
        .fromTo('[data-hero-titulo] .linha', { yPercent: 110, opacity: 0 }, { yPercent: 0, opacity: 1, stagger: 0.12, duration: 1 }, 0.25)
        .fromTo('[data-hero-sub]', { y: 18, opacity: 0 }, { y: 0, opacity: 1 }, 0.6)
        .fromTo('[data-hero-cta]', { y: 14, opacity: 0 }, { y: 0, opacity: 1, stagger: 0.08 }, 0.8);
}

/* ---------- A linha dourada do "Território / Responsabilidade" e afins ---------- */
function initLinhaDourada() {
    gsap.utils.toArray('[data-anim="linha"]').forEach((el) => {
        gsap.fromTo(
            el,
            { scaleX: 0 },
            {
                scaleX: 1,
                duration: 1.2,
                ease: 'power2.inOut',
                scrollTrigger: { trigger: el, start: 'top 90%', once: true },
            }
        );
    });
}

/* ---------- Swiper: portfólio + depoimentos ---------- */
function initCarrosseis(reducedMotion) {
    const portfolio = document.querySelector('[data-swiper="portfolio"]');
    if (portfolio) {
        const secaoPortfolio = portfolio.closest('section') ?? portfolio;
        new Swiper(portfolio, {
            modules: [Navigation, Pagination, A11y],
            slidesPerView: 1.15,
            spaceBetween: 16,
            grabCursor: true,
            a11y: { enabled: true },
            pagination: { el: portfolio.querySelector('[data-swiper-pagination]'), clickable: true },
            navigation: {
                nextEl: secaoPortfolio.querySelector('[data-swiper-next]'),
                prevEl: secaoPortfolio.querySelector('[data-swiper-prev]'),
            },
            breakpoints: {
                640: { slidesPerView: 2.2, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 24 },
            },
        });
    }

    const depo = document.querySelector('[data-swiper="depoimentos"]');
    if (depo) {
        new Swiper(depo, {
            modules: [Pagination, A11y, Autoplay],
            slidesPerView: 1,
            spaceBetween: 24,
            loop: depo.querySelectorAll('.swiper-slide').length > 2,
            autoplay: reducedMotion ? false : { delay: 6000, disableOnInteraction: true },
            pagination: { el: depo.querySelector('[data-swiper-pagination]'), clickable: true },
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 24 },
            },
        });
    }
}
