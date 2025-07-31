/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./app/**/*.php",
    ],
    darkMode: 'class', // Enable class-based dark mode
    theme: {
        extend: {
            // Override default border radius to 5px for consistency
            borderRadius: {
                'none': '0px',
                'sm': '5px',    // Override default 2px
                'DEFAULT': '5px', // Override default 4px
                'md': '5px',    // Override default 6px
                'lg': '5px',    // Override default 8px
                'xl': '5px',    // Override default 12px
                '2xl': '5px',   // Override default 16px
                '3xl': '5px',   // Override default 24px
                'full': '5px',  // Override full rounded for consistency
            },
            // Acumen Craft Brand Colors from STYLE_GUIDE.md
            colors: {
                'acumen': {
                    'black': '#090909',
                    'dark-gold': '#c28840',
                    'medium-gold': '#d4a743',
                    'light-gold': '#f0c75e',
                    'white': '#f9f9f9',
                },
                // Dark mode input colors for form visibility
                'dark-input': '#292929',
                'dark-text': '#e5e7eb',
                'dark-border': '#4b5563',
                'dark-placeholder': '#9ca3af',
            },
            // Acumen Craft Typography from STYLE_GUIDE.md
            fontFamily: {
                'acumen-primary': ['extrasquare', 'NovaSquare', 'serif'],
                'acumen-headings': ['extrasquareCaps', 'NovaSquare', 'serif'],
            },
            // Font sizes from STYLE_GUIDE.md
            fontSize: {
                'h1': '36px',
                'h2': '28px',
                'h3': '24px',
                'h4': '20px',
                'h5': '17px',
                'h6': '14px',
                'body': '13px',
            },
        },
    },
    plugins: [],
}
