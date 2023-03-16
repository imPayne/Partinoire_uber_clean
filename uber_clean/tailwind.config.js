/** @type {import('tailwindcss').Config} */
module.exports = {

  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    "./node_modules/flowbite/**/*.js",
  ],

  theme: {
    extend: {
      fontFamily: {
        "nunito": ['"Nunito Sans"']
      },
    },
  },
  plugins: [
    require('flowbite/plugin')
  ],
}
