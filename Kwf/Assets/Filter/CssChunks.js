require('es6-promise').polyfill(); //required for older nodejs

var postcss = require('postcss');
var postcssChunk = require('postcss-chunk');

var instance = postcss([
    postcssChunk({ size: 4000 })
]);
var css = '';
process.stdin.setEncoding('utf-8')
process.stdin.on('data', function(buf) { css += buf.toString(); });
process.stdin.on('end', function() {
    instance.process(css).then(function (result) {
        var first = true;
        result.chunks.forEach(function(chunk) {
            if (!first) {
                process.stdout.write("\n/* ***** NEXT CHUNK ***** */\n");
            }
            process.stdout.write(chunk.css);
            first = false;
        });
    }).catch(function(e) {
        console.log(e);
        process.exit(1);
    });
});
process.stdin.resume();
