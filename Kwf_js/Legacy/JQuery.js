//leaks jQuery to window for non commonjs (legacy) usage
window.jQuery = window.$ = require('jQuery');
