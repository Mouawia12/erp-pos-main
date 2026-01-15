/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/**/*.vue',
    ],
    corePlugins: {
        preflight: false,
    },
    theme: {
        extend: {},
    },
    plugins: [],
};
