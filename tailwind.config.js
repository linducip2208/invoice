import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Quicksand', 'Figtree', ...defaultTheme.fontFamily.sans],
                display: ['"Comfortaa"', '"Quicksand"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                unicorn: {
                    pink:    '#FF7AC6',
                    rose:    '#FFB1D8',
                    blush:   '#FFE4F1',
                    purple:  '#A78BFA',
                    lilac:   '#C4B5FD',
                    lavender:'#EDE9FE',
                    blue:    '#7DD3FC',
                    sky:     '#BAE6FD',
                    mist:    '#E0F2FE',
                    mint:    '#86EFAC',
                    peach:   '#FDBA74',
                    cream:   '#FEF3C7',
                    gold:    '#FCD34D',
                    star:    '#FFFBEB',
                    horn:    '#F59E0B',
                    midnight:'#312E81',
                    twilight:'#4C1D95',
                },
            },
            backgroundImage: {
                'unicorn-gradient':
                    'linear-gradient(135deg, #FF7AC6 0%, #A78BFA 25%, #7DD3FC 55%, #86EFAC 85%, #FCD34D 100%)',
                'unicorn-soft':
                    'linear-gradient(135deg, #FFE4F1 0%, #EDE9FE 30%, #E0F2FE 65%, #DCFCE7 100%)',
                'unicorn-rainbow':
                    'linear-gradient(90deg, #FF7AC6, #A78BFA, #7DD3FC, #86EFAC, #FCD34D, #FB923C, #F472B6)',
                'unicorn-aurora':
                    'radial-gradient(ellipse at top left, #FFB1D8 0%, transparent 50%), radial-gradient(ellipse at top right, #BAE6FD 0%, transparent 50%), radial-gradient(ellipse at bottom right, #C4B5FD 0%, transparent 50%), radial-gradient(ellipse at bottom left, #FEF3C7 0%, transparent 50%)',
                'unicorn-sparkle':
                    "radial-gradient(circle at 25% 25%, rgba(255,255,255,0.8) 0%, transparent 1.5%), radial-gradient(circle at 75% 75%, rgba(255,255,255,0.8) 0%, transparent 1.5%), radial-gradient(circle at 60% 30%, rgba(252,211,77,0.6) 0%, transparent 1%), radial-gradient(circle at 30% 80%, rgba(167,139,250,0.5) 0%, transparent 1.2%)",
                'unicorn-mesh':
                    'radial-gradient(at 20% 20%, #FFB1D8 0px, transparent 50%), radial-gradient(at 80% 0%, #C4B5FD 0px, transparent 50%), radial-gradient(at 0% 50%, #BAE6FD 0px, transparent 50%), radial-gradient(at 80% 80%, #FCD34D 0px, transparent 50%), radial-gradient(at 0% 100%, #86EFAC 0px, transparent 50%)',
            },
            boxShadow: {
                'unicorn':       '0 10px 30px -10px rgba(167, 139, 250, 0.45), 0 6px 20px -6px rgba(255, 122, 198, 0.3)',
                'unicorn-lg':    '0 20px 50px -12px rgba(167, 139, 250, 0.5), 0 12px 30px -10px rgba(255, 122, 198, 0.35)',
                'unicorn-glow':  '0 0 0 1px rgba(255,255,255,0.6) inset, 0 12px 40px -8px rgba(167, 139, 250, 0.55), 0 0 30px -5px rgba(255, 122, 198, 0.45)',
                'unicorn-pink':  '0 8px 25px -8px rgba(255, 122, 198, 0.55)',
                'unicorn-blue':  '0 8px 25px -8px rgba(125, 211, 252, 0.6)',
                'unicorn-gold':  '0 8px 25px -8px rgba(252, 211, 77, 0.6)',
                'unicorn-card':  '0 4px 24px -6px rgba(167, 139, 250, 0.18), 0 2px 6px -2px rgba(255, 122, 198, 0.12)',
                'unicorn-inner': 'inset 0 2px 4px 0 rgba(255, 255, 255, 0.6), inset 0 -2px 4px 0 rgba(167, 139, 250, 0.15)',
            },
            animation: {
                'unicorn-float':   'unicorn-float 6s ease-in-out infinite',
                'unicorn-shine':   'unicorn-shine 3s linear infinite',
                'unicorn-bounce':  'unicorn-bounce 2.5s ease-in-out infinite',
                'unicorn-pulse':   'unicorn-pulse 2s ease-in-out infinite',
                'unicorn-spin':    'unicorn-spin 8s linear infinite',
                'unicorn-rainbow': 'unicorn-rainbow 6s ease infinite',
                'unicorn-twinkle': 'unicorn-twinkle 1.8s ease-in-out infinite',
                'unicorn-wiggle':  'unicorn-wiggle 1.2s ease-in-out infinite',
            },
            keyframes: {
                'unicorn-float': {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%':      { transform: 'translateY(-8px)' },
                },
                'unicorn-shine': {
                    '0%':   { backgroundPosition: '-200% center' },
                    '100%': { backgroundPosition: '200% center' },
                },
                'unicorn-bounce': {
                    '0%, 100%': { transform: 'translateY(0) rotate(0deg)' },
                    '50%':      { transform: 'translateY(-4px) rotate(-2deg)' },
                },
                'unicorn-pulse': {
                    '0%, 100%': { boxShadow: '0 0 0 0 rgba(255, 122, 198, 0.5)', transform: 'scale(1)' },
                    '50%':      { boxShadow: '0 0 0 12px rgba(255, 122, 198, 0)',   transform: 'scale(1.02)' },
                },
                'unicorn-spin': {
                    '0%':   { transform: 'rotate(0deg)' },
                    '100%': { transform: 'rotate(360deg)' },
                },
                'unicorn-rainbow': {
                    '0%, 100%': { backgroundPosition: '0% 50%' },
                    '50%':      { backgroundPosition: '100% 50%' },
                },
                'unicorn-twinkle': {
                    '0%, 100%': { opacity: '0.3', transform: 'scale(0.8)' },
                    '50%':      { opacity: '1',   transform: 'scale(1.1)' },
                },
                'unicorn-wiggle': {
                    '0%, 100%': { transform: 'rotate(-3deg)' },
                    '50%':      { transform: 'rotate(3deg)' },
                },
            },
            borderRadius: {
                'unicorn': '1.25rem',
            },
        },
    },

    plugins: [forms],
};
