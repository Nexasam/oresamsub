import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class', // 👈 important
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        "./resources/**/*.jsx",
        "./resources/**/*.js",
    ],

    theme: {
        extend: {
            // fontFamily: {
            //     sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            // },
            fontFamily: {
                sans: ['"Work Sans"', 'sans-serif'],
                // sans: ["Inter", "ui-sans-serif", "system-ui"],
            },
            colors: {
                primary: { "50": "#eff6ff", "100": "#dbeafe", "200": "#bfdbfe", "300": "#93c5fd", "400": "#60a5fa", "500": "#3b82f6", "600": "#2563eb", "700": "#1d4ed8", "800": "#1e40af", "900": "#1e3a8a" },
                emerald: {
                    "50": "#F3FCF8",
                    "100": "#DDF8EE",
                    "200": "#B8EDDB",
                    "300": "#80DDBE",
                    "400": "#3FC69B",
                    "500": "#16A878",
                    "600": "#0A8F68",
                    "700": "#087657",
                    "800": "#064E3B",
                    "900": "#053D30",
                    "950": "#02271E"
                }
            },
        },
    },

    plugins: [forms],
};
