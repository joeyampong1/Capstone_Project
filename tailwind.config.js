import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
   
    darkMode: 'class',

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
                primary: {
                    50: '#fef3ed',
                    100: '#fce3d4',
                    200: '#f8c5a8',
                    300: '#f5a67d',
                    400: '#f18851',
                    500: '#f07a3a',
                    600: '#d8652a',
                    700: '#b85222',
                    800: '#943f1a',
                    900: '#703012',
                    DEFAULT: '#f07a3a',
                },
                accent: {
                    50: '#edf7f3',
                    100: '#d4eee6',
                    200: '#a9ddcc',
                    300: '#7ecbb3',
                    400: '#53ba99',
                    500: '#39ac8c',
                    600: '#2d8a70',
                    700: '#216854',
                    800: '#154638',
                    900: '#0a231c',
                    DEFAULT: '#39ac8c',
                },
                secondary: {
                    DEFAULT: '#f3ede6',
                },
            },
        },
    },

    plugins: [forms],
};