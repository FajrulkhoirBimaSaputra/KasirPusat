import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },

            colors: {
                primary: {
                    DEFAULT: "#C21F1F",
                    dark: "#A61A1A",
                    light: "#D84A4A",
                },

                secondary: {
                    DEFAULT: "#FFFFFF",
                },

                accent: {
                    DEFAULT: "#E5CD91",
                },

                dark: {
                    DEFAULT: "#423433",
                },
            },
        },
    },

    plugins: [forms],
};
