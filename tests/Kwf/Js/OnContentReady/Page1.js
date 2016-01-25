var onReady = require('kwf/on-ready');
window.$ = require('jQuery'); //leak for qunit

onReady.onRender('.foo', function(el) {
    el.html('bar');
});
