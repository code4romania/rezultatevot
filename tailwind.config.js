export default {
    darkMode: 'selector',
    content: [
        //,
        './app/Filament/**/*.php',
        './app/Livewire/**/*.php',
        './app/View/Components/**/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
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
                purple: {
                    50: '#F5F1F8',
                    100: '#E9E0F0',
                    200: '#D3C1E1',
                    300: '#BCA3D2',
                    400: '#A987C4',
                    500: '#9369B5',
                    600: '#7C4FA1',
                    700: '#644082',
                    800: '#4C3163',
                    900: '#352245',
                    950: '#1A1122',
                },
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/container-queries'),
    ],
};
