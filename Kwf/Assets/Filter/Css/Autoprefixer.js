require('es6-promise').polyfill(); //required for older nodejs

var autoprefixer = require('autoprefixer');
var postcss = require('postcss');

var prefixer = postcss([ autoprefixer({ add: true, remove: true, browsers: ['> 0.05%'] }) ]);

var css = '';
process.stdin.setEncoding('utf-8')
process.stdin.on('data', function(buf) { css += buf.toString(); });
process.stdin.on('end', function() {
    prefixer.process(css).then(function (result) {
        process.stdout.write(result.css);
    }).catch(function(e) {
        console.log(e);
        process.exit(1);
    });
});
process.stdin.resume();
