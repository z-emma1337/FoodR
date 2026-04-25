export default {
  darkMode: ["class"],
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.vue",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-10px)' },
        },
        'card-gradient': {
          '0%, 100%': { backgroundPosition: '0% 50%' },
          '50%': { backgroundPosition: '100% 50%' },
        },
      },
      animation: {
        float: 'float 4s ease-in-out infinite',
        'card-gradient': 'card-gradient 6s ease infinite',
      },
      screens: {
      sm: '640px',
      md: '768px',
      lg: '1024px',
      midxl: '1150px',
      xl: '1280px',
      '2xl': '1536px',
      xxl: '1700px',
    }
  }
  },  plugins: [],
}