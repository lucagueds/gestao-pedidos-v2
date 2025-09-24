/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],

    theme: {
        extend: {
            // Adicione suas cores aqui
            colors: {
                'tiny-blue': '#0073cf',
                'tiny-blue-hover': '#005a9e',
                'bg-light': '#f4f5f7',
                'border-gray': '#dfe1e6',
                'text-dark': '#172b4d',
                'text-light': '#5e6c84',
            }
        },
    },

    plugins: [],
}
