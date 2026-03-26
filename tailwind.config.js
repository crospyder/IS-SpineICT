export default {
    darkMode: 'class',
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
    ],
    theme: {
        extend: {
            colors: {
                app: {
                    bg: '#f9fafb',
                    surface: '#ffffff',
                    border: '#e5e7eb',
                    text: '#111827',

                    dark: {
                        bg: '#0f172a',
                        surface: '#1e293b',
                        border: '#334155',
                        text: '#e5e7eb',
                    }
                },

                brand: {
                    primary: '#2563eb',
                    danger: '#dc2626',
                    warning: '#f59e0b',
                    success: '#16a34a',
                }
            }
        },
    },
    plugins: [],
}