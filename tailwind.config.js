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
                sans: ['Plus Jakarta Sans', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                maroon: {
                    deep:    '#380F17',
                    crimson: '#8F0B13',
                    50:      '#FDF2F2',
                    100:     '#FBE4E5',
                    200:     '#F5B8BA',
                    300:     '#E87D82',
                    400:     '#B82A33',
                    700:     '#8F0B13',
                    800:     '#5A121D',
                    900:     '#380F17',
                },
                cream: {
                    DEFAULT: '#EFDFC5',
                    50:      '#FAF6F0',
                    100:     '#F5EFE6',
                    200:     '#EFDFC5',
                    300:     '#DFC9A6',
                    border:  '#EAE0D3',
                },
                charcoal: {
                    dark:   '#252B2B',
                    medium: '#4C4F54',
                    light:  '#7A7E85',
                },
                status: {
                    available: '#22C55E',
                    pending:   '#D97706',
                    booked:    '#EF4444',
                },
            },
        },
    },

    plugins: [forms],
};
