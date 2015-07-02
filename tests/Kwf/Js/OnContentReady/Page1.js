var onReady = require('kwf/on-ready');

onReady.onRender('.foo', function(el) {
    el.html('bar');
});
