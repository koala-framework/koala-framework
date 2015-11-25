require('es6-promise').polyfill(); //required for older nodejs

var postcss = require('postcss');
var mqdr = require('postcss-media-queries-drop-redundant');

var media = postcss([mqdr]);

var css = '';
process.stdin.setEncoding('utf-8')
process.stdin.on('data', function(buf) { css += buf.toString(); });
process.stdin.on('end', function() {
    media.process(css).then(function (result) {
        process.stdout.write(result.css);
    }).catch(function(e) {
        process.exit(1);
    });
});
process.stdin.resume();

