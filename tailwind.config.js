/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './**/*.php',
    './assets/js/src/**/*.js',
    './template-parts/**/*.php',
    './woocommerce/**/*.php',
    './page-templates/**/*.php',
  ],
  theme: {
    extend: {
      screens: {
        'sm': '375px',
        'tablet-sm': '640px',
        'tablet': '768px',
        'md': '810px',
        'lg': '1024px',
        'xl': '1280px',
        'xxl': '1360px',
      },
      maxWidth: {
        'site': '1280px',
        'site-xxl': '1360px',
      },
      colors: {
        primary: {
          DEFAULT: 'var(--color-primary, #E15726)',
          light: 'var(--color-primary-light, #FCEEE9)',
        },
        gray: {
          50: '#F9FAFB',
          300: '#E2E2E2',
          500: '#CACACA',
          600: '#6E6E6E',
          700: '#2C2C2C',
        },
        green: {
          DEFAULT: '#36B37E',
        },
        red: {
          DEFAULT: '#E34850',
        },
        'black-dark': '#1A1A1A',
        'black-light': '#4B4B4B',
        blue: {
          100: '#ECF3FF',
          500: '#4B7DF3',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
