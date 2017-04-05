var onReady = require('kwf/commonjs/on-ready');
window.$ = require('jquery'); //leak for qunit

onReady.onRender('.foo', function(el) {
    el.html('bar');
});
