export default {
    darkMode: 'selector',
    content: [
        //,
        './app/Filament/**/*.php',
        './app/Livewire/**/*.php',
        './app/View/Components/**/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/livewire/**/*.blade.php',
    ],
    theme: {
        container: ({ theme }) => ({
            center: true,
            padding: {
                DEFAULT: '1rem',
                sm: '1.5rem',
                lg: '2rem',
            },
        }),
        extend: {
            colors: {
                custom: 'rgb(var(--color-custom))',
            },
        },
    },
    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
