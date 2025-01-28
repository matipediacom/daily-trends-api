import colors from 'tailwindcss/colors';

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./templates/**/*.{html.twig}"],
    theme: {
        extend: {
            colors: {
                primary: colors.sky,
            }
        },
    },
    plugins: [],
}
