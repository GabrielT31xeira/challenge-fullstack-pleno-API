/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        "./index.html",                   // necessário
        "./src/**/*.{vue,ts,js,jsx,tsx}" // cobre todos os componentes Vue + TS
    ],
    theme: {
        extend: {},
    },
    plugins: [],
}
