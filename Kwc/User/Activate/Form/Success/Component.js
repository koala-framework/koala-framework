var onReady = require('kwf/on-ready');

onReady.onRender('.kwc-class', function(el, config) {
    window.setTimeout(function() {
        window.location.href = config.redirectUrl;
    }, 3000);
});
