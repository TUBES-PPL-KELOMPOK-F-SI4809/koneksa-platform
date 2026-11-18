import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', '"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', '"Fira Code"', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                canvas: '#F4F6F9',
                hairline: '#E2E8F0',
                ink: {
                    DEFAULT: '#0F172A',
                    hover: '#1E293B',
                    deep: '#020617',
                    secondary: '#475569',
                    muted: '#64748B',
                },
                accent: {
                    50: '#EFF6FF',
                    500: '#0070F3',
                    600: '#0060DF',
                    700: '#1D4ED8',
                },
                primary: {
                    50: '#EEF2FF',
                    100: '#E0E7FF',
                    200: '#C7D2FE',
                    300: '#A5B4FC',
                    400: '#818CF8',
                    500: '#6366F1',
                    600: '#4F46E5',
                    700: '#4338CA',
                    800: '#3730A3',
                    900: '#312E81',
                },
                secondary: {
                    50: '#ECFEFF',
                    100: '#CFFAFE',
                    200: '#A5F3FC',
                    500: '#06B6D4',
                    600: '#0891B2',
                    700: '#0E7490',
                },
                neutral: {
                    50: '#F8FAFC',
                    100: '#F1F5F9',
                    200: '#E2E8F0',
                    300: '#CBD5E1',
                    400: '#94A3B8',
                    500: '#64748B',
                    600: '#475569',
                    700: '#334155',
                    800: '#1E293B',
                    900: '#0F172A',
                    950: '#020617',
                },
                success: {
                    light: '#ECFDF5',
                    DEFAULT: '#10B981',
                    dark: '#047857',
                },
                warning: {
                    light: '#FFFBEB',
                    DEFAULT: '#F59E0B',
                    dark: '#B45309',
                },
                danger: {
                    light: '#FFF1F2',
                    DEFAULT: '#E11D48',
                    dark: '#BE123C',
                },
                info: {
                    light: '#EFF6FF',
                    DEFAULT: '#0070F3',
                    dark: '#1D4ED8',
                },
                sidebar: {
                    DEFAULT: 'var(--sidebar)',
                    foreground: 'var(--sidebar-foreground)',
                    primary: 'var(--sidebar-primary)',
                    'primary-foreground': 'var(--sidebar-primary-foreground)',
                    accent: 'var(--sidebar-accent)',
                    'accent-foreground': 'var(--sidebar-accent-foreground)',
                    border: 'var(--sidebar-border)',
                    ring: 'var(--sidebar-ring)',
                },
            },
            boxShadow: {
                xs: '0 1px 2px 0 rgb(15 23 42 / 0.05)',
                paas: '0 4px 12px -2px rgb(15 23 42 / 0.08)',
            },
        },
    },

    plugins: [forms],
};
