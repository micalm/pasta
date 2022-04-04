module.exports = {
    content: [
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    ],
    theme: {
        fontFamily: {
            sans: ['Comfortaa', 'sans-serif'],
            mono: ['Fira Mono', 'Courier New', 'Courier', 'monospace'],
        },
        colors: {
            'nord': {
                'night': {
                    'base': '#2e3440',
                    'light': '#3b4252',
                    'lighter': '#434c5e',
                    'lightest': '#4c566a',
                },
                'storm': {
                    'base': '#d8dee9',
                    'light': '#e5e9f0',
                    'lighter': '#eceff4',
                },
                'frost': {
                    'green': '#8fbcbb',
                    'cyan': '#88c0d0',
                    'blue': '#81a1c1',
                    'bluer': '#5e81ac',
                },
                'aurora': {
                    'red': '#bf616a',
                    'orange': '#d08770',
                    'yellow': '#ebcb8b',
                    'green': '#a3be8c',
                    'purple': '#b48ead',
                }
            },
        }
    },
    plugins: [],
}
