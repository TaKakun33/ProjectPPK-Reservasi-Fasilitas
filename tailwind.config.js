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
                navy: {
                    50: '#EFF4FF',
                    100: '#DBE4FF',
                    200: '#BFD0FF',
                    600: '#2547B8',
                    700: '#1E3A8A',
                    800: '#172F6E',
                    900: '#101F4A',
                },
                gold: {
                    300: '#FDE047',
                    400: '#EAB308',
                    500: '#CA8A04',
                    600: '#A16207',
                },
                cream: '#F8F9FA',
                status: {
                    available: '#22C55E',
                    pending: '#EAB308',
                    booked: '#EF4444',
                },
            },
        },
    },

    plugins: [forms],
};
