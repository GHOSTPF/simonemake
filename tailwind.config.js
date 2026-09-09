/**
 * Tailwind CSS — Simone Gomes
 *
 * PALETA DA MARCA (edite os hex aqui para mudar as cores do site inteiro):
 *   branco  #FFFFFF  fundo dominante
 *   bege    #EFE6DA  fundo dominante / seções alternadas
 *   areia   #D9C7A6  apoio, backgrounds suaves
 *   dourado #C7A977  SÓ EM DETALHE — fio, moldura, ícones, hover. Nunca fundo grande.
 *   grafite #2B2724  texto principal
 *
 * Regra visual: branco e bege dominam; dourado só como acento fino; grafite na tipografia.
 * Sem gradientes vibrantes — a sofisticação vem da sobriedade.
 */

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/**/*.php', // pega classes usadas em config/institucional.php via Blade
    ],
    theme: {
        extend: {
            colors: {
                branco: '#FFFFFF',
                bege: '#EFE6DA',
                areia: '#D9C7A6',
                dourado: {
                    DEFAULT: '#C7A977',
                    // tons auxiliares p/ hover/borda — mantidos discretos
                    600: '#B4915C',
                    700: '#9A7A48',
                },
                grafite: {
                    DEFAULT: '#2B2724',
                    700: '#3D3833',
                    500: '#6B635B',
                },
            },
            fontFamily: {
                // Serifada elegante para títulos, sans discreta para texto.
                serif: ['"Cormorant Garamond"', 'Georgia', 'ui-serif', 'serif'],
                sans: ['"Inter"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            letterSpacing: {
                kicker: '0.22em',
            },
            maxWidth: {
                prosa: '68ch',
            },
            transitionTimingFunction: {
                suave: 'cubic-bezier(0.22, 1, 0.36, 1)',
            },
            keyframes: {
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(16px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
            animation: {
                'fade-up': 'fade-up 0.7s cubic-bezier(0.22, 1, 0.36, 1) both',
            },
        },
    },
    plugins: [],
};
