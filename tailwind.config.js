/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        'opag-dark':  '#1a3a1a',
        'opag-green': '#2d5a1b',
        'opag-mid':   '#3d7a2a',
      },
    },
  },
  plugins: [],
}