var csc = require('css-selectors-count');

var css = '';
process.stdin.setEncoding('utf-8')
process.stdin.on('data', function(buf) { css += buf.toString(); });
process.stdin.on('end', function() {
    console.log(JSON.stringify(csc(css)));
});
process.stdin.resume();
