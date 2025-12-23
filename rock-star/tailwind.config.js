/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./pages/**/*.{js,ts,jsx,tsx}",
        "./components/**/*.{js,ts,jsx,tsx}",
    ],
    darkMode: 'class',
    important: '.landing-template-wrapper',
    corePlugins: {
        preflight: false,
    },
    theme: {
        extend: {
            screens: {
                '2xl': '1320px',
            },
            container: {
                screens: {
                    '2xl': '1320px',
                }
            }
        },
    },
    plugins: [],
}
