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
            colors: {
                primary: '#2563EB',
                dark: '#090E34',
            },
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
