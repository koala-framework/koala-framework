require('es6-promise').polyfill(); //required for older nodejs

var postcss = require('postcss');
var prefixKeyframes = require('postcss-prefixer-keyframes');
var args = process.argv.slice(2);
var prefix = postcss([ prefixKeyframes({prefix: args[0]}) ]);

var css = '';
process.stdin.setEncoding('utf-8')
process.stdin.on('data', function(buf) { css += buf.toString(); });
process.stdin.on('end', function() {
    prefix.process(css).then(function (result) {
        process.stdout.write(result.css);
    }).catch(function(e) {
        process.exit(1);
    });
});
process.stdin.resume();
