var mdeps = require('module-deps');
var fs = require('fs');
var path = require('path');

var argv = process.argv.slice(2);

var JSONStream = require('JSONStream');


var files = argv.map(function (file) {
    if (file === '-') return process.stdin;
    return path.resolve(file);
});

var md = mdeps({
    resolve: function(id, parent, cb) {
        cb(null, id);
    },
    filter: function(id, parent, cb) {
        return false;
    }
});
md.pipe(JSONStream.stringify()).pipe(process.stdout);

files.forEach(function (file) { md.write(file) });
md.end();

