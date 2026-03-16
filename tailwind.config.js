const animate = require('tailwindcss-animate');

function rgb(variable) {
  return `rgb(var(${variable}) / <alpha-value>)`;
}

/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: ['class'],
  content: [
    './resources/**/*.{vue,js,ts,jsx,tsx}',
    './node_modules/shadcn-vue/dist/**/*.js',
  ],
  theme: {
    container: {
      center: true,
      padding: '2rem',
      screens: {
        '2xl': '1400px',
      },
    },
    extend: {
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
        mono: ['JetBrains Mono', 'monospace'],
      },
      colors: {
        surface: rgb('--color-surface'),
        'surface-raised': rgb('--color-surface-raised'),
        'surface-overlay': rgb('--color-surface-overlay'),
        border: rgb('--color-border'),
        'border-strong': rgb('--color-border-strong'),
        primary: rgb('--color-text-primary'),
        secondary: rgb('--color-text-secondary'),
        muted: rgb('--color-text-muted'),
        accent: {
          DEFAULT: rgb('--color-accent'),
          hover: rgb('--color-accent-hover'),
          subtle: rgb('--color-accent-subtle'),
          text: rgb('--color-accent-text'),
        },
        success: {
          DEFAULT: rgb('--color-success'),
          subtle: rgb('--color-success-subtle'),
        },
        warning: {
          DEFAULT: rgb('--color-warning'),
          subtle: rgb('--color-warning-subtle'),
        },
        danger: {
          DEFAULT: rgb('--color-danger'),
          subtle: rgb('--color-danger-subtle'),
        },
      },
      keyframes: {
        'accordion-down': {
          from: { height: 0 },
          to: { height: 'var(--radix-accordion-content-height)' },
        },
        'accordion-up': {
          from: { height: 'var(--radix-accordion-content-height)' },
          to: { height: 0 },
        },
        shimmer: {
          '0%': { backgroundPosition: '200% 0' },
          '100%': { backgroundPosition: '-200% 0' },
        },
        'fade-up': {
          from: { opacity: '0', transform: 'translateY(6px)' },
          to: { opacity: '1', transform: 'translateY(0)' },
        },
        'scale-in': {
          from: { opacity: '0', transform: 'scale(0.97)' },
          to: { opacity: '1', transform: 'scale(1)' },
        },
      },
      animation: {
        'accordion-down': 'accordion-down 0.2s ease-out',
        'accordion-up': 'accordion-up 0.2s ease-out',
        shimmer: 'shimmer 1.5s infinite',
        'fade-up': 'fade-up 0.2s ease-out',
        'scale-in': 'scale-in 0.15s ease-out',
      },
    },
  },
  plugins: [animate, require('@tailwindcss/forms')],
};
