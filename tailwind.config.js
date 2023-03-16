/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        container: {
            center: true,
            screens: {
                sm: "600px",
                md: "728px",
                lg: "984px",
                xl: "1240px",
            },
        },
    },
    plugins: [require("@tailwindcss/forms")],
};
