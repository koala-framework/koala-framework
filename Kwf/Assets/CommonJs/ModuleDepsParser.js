var mdeps = require('module-deps');
var fs = require('fs');
var path = require('path');
var insert = require('insert-module-globals');
var processPath = require.resolve('process/browser.js');

var argv = process.argv.slice(2);

var JSONStream = require('JSONStream');


var files = argv.map(function (file) {
    if (file === '-') return process.stdin;
    return path.resolve(file);
});

var md = mdeps({
    transform: function(file) {
        return insert(file, {
            basedir: 'node_modules',
            vars: {
                // because default process return wrong path (../../process/browser.js)
                process: function (file, basedir) {
                    var relpath = path.relative(basedir, processPath);
                    return 'require(' + JSON.stringify(relpath) + ')';
                }
            }
        });
    },
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

