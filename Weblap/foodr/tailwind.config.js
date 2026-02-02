export default {
  darkMode: ["class"],
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.vue",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {}
  },
  plugins: [],
}


module.exports = {
  theme: {
    extend: {
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-10px)' },
        },
      },
      animation: {
        float: 'float 4s ease-in-out infinite',
      },
    },
  },
}