/** @type {import('tailwindcss').Config} */
export default {
  darkMode: ["class"],
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.vue",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          DEFAULT: '#b21f24',      // alap szín
          50: '#fbeaea',
          100: '#f5d2d3',
          200: '#ebb3b5',
          300: '#e39597',
          400: '#da7678',
          500: '#b21f24',          // fő szín
          600: '#901a1d',
          700: '#6e1417',
          800: '#4c0f11',
          900: '#2a0a0a'
        }
      }
    }
  },
  plugins: [],
}
