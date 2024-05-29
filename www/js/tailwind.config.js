/** @type {import('tailwindcss').Config} */
const TW_settings = {
    content: ["./www/**/*.{html,js}", "app/Web/Presenters/**/*.{html,js,latte}"],
    theme: {
        extend: {
            colors: {
                transparent: 'transparent',
                current: 'currentColor',
                'brand': '#1B264F',
                "brand-light": "#EAF7FF",
                'brand-secondary': '#005689',
                'secondary': '#797979',
                'secondary-2': '#403E3E',
                'user': '#FF7846',
            },
        }
    },
    plugins: [],
};
if(typeof module !== "undefined") {
    module.exports = TW_settings;
} else {
    tailwind.exports = TW_settings;
}