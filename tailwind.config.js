const defaultTheme = require('tailwindcss/defaultTheme')
import common from './tailwind.common.js'

export default {
    safelist: [
        // Nav glass effect — Alpine :class dynamic strings not picked up by static scanner
        'backdrop-blur-none',
        'backdrop-blur-md',
        'bg-gradient-to-b',
        'from-transparent',
        'to-transparent',
        'from-neutral-950/90',
        'to-neutral-950/60',
        'from-neutral-950/0',
        'to-neutral-950/0',
        'border-b-transparent',
        'border-b-white/[0.08]',
        'shadow-none',
        // Home page hero backgrounds
        'bg-neutral-950',
        'bg-neutral-900',
        'bg-white/5',
        'bg-white/10',
        'bg-white/[0.02]',
        'bg-white/[0.03]',
        'bg-white/[0.04]',
        'bg-white/[0.05]',
        'border-white/5',
        'border-white/8',
        'border-white/10',
        'border-white/20',
        'hover:bg-white/[0.04]',
        'hover:bg-white/[0.05]',
        'hover:border-white/10',
        'hover:border-white/20',
    ],
    plugins: [require("daisyui")],
    daisyui: {
        themes: ["light"],
    },
    content: [
        "./resources/**/*.blade.php",
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './resources/views/site/**/*.blade.php',
        './resources/views/repo/**/*.blade.php',
        './resources/views/vendor/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        "./resources/**/*.js",
        "./resources/**/*.vue",
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    darkMode: 'class',
    theme: {
        fontSize: {
            xxs: '0.65rem',
            xs: '0.75rem',
            sm: '0.9rem',
            base: '1rem',
            lg: '1.125rem',
            xl: '1.25rem',
            '2xl': '1.5rem',
            '3xl': '1.75rem',
            '4xl': '2.5rem',
            '5xl': '3rem',
            '6xl': '3.43rem',
            '7xl': '4.5rem',
        },
        extend: {
            fontFamily: {
                'sans': ['"Poppins"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: common.colors.primary,
                secondary: common.colors.secondary,
                
            },
            lineHeight: {
                'xxs': '1.1',
                'xs': '1.2',
            },
            height: {
                '180': '45rem',
            },
            dropShadow: {
                '3xl': '0 35px 35px rgba(0, 0, 0, 0.25)',
                '4xl': [
                    '0 35px 35px rgba(0, 0, 0, 0.25)',
                    '0 45px 65px rgba(0, 0, 0, 0.15)'
                ]
            },
            blur: {
                '4xl': '75px',
            },
            listStyleImage: {
                checkmark: 'url("/images/check.svg") ',
            },
            scale: {
                '101': '1.01',
                '102': '1.02',
                '103': '1.03',
            }
        },
    }
}
