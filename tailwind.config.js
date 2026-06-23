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
                sans: ['DM Sans', 'Figtree', ...defaultTheme.fontFamily.sans],
                heading: ['Sora', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#fdf0eb',
                    100: '#f5ddd3',
                    200: '#ebc4b4',
                    300: '#dfa088',
                    400: '#d17a5c',
                    500: '#c1623d',
                    600: '#c1623d',
                    700: '#a85334',
                    800: '#8f462c',
                    900: '#172d40',
                    950: '#0f1e2b',
                },
                navy: {
                    DEFAULT: '#172d40',
                    dark: '#0f1e2b',
                    light: '#1e3a52',
                },
                cream: '#faf8f5',
                line: '#e4e8ec',
                muted: '#7a8a98',
                body: '#3a4a58',
            },
        },
    },

    plugins: [forms],
};
