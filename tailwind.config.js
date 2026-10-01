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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                maroon: {
                    50:  '#F7ECEC',
                    100: '#EDD3D5',
                    200: '#D9A5AB',
                    300: '#B56B75',
                    400: '#8C3A44',
                    700: '#6D2932',
                    800: '#561C24',
                    900: '#3A1018',
                },
                cream: {
                    DEFAULT: '#E8D8C4',
                    100: '#F5EEE4',
                    200: '#E8D8C4',
                    300: '#C7B7A3',
                    400: '#A89880',
                },
                status: {
                    available: '#22C55E',
                    pending: '#D97706',
                    booked:   '#EF4444',
                },
            },
        },
    },

    plugins: [forms],
};
