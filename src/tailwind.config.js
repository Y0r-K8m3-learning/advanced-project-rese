import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
 safelist: [{ pattern: /^border-/ }, { pattern: /^bg-/ }, { pattern: /^text-/ }],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
             // Set theme colors (Required config!)
      colors: {
          white: {
    300: "#F8F8F8",
    500: "#fff",
  },
  gray: {
    100: "#EEEFF2",
    400: "#AFB5C0",
    500: "#DDDDDD",
  },
  red: {
    500: "#ef4444",
  },
      },
        },
    },

    plugins: [forms],
};
