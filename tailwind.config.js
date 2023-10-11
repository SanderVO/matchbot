/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        colors: {
            transparent: "transparent",
            current: "currentColor",
            gray: "#294D61",
            white: "white",
            "green-white": "#6DA5C0",
            morning: "#0F969C",
            day: "#0C7075",
            evening: "#072E33",
            midnight: "#05161A",
        },
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
