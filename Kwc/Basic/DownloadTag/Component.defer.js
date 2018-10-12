var onReady = require('kwf/commonjs/on-ready');
var statistics = require('kwf/commonjs/statistics');

onReady.onRender('.kwcClass', function (el, config) {

    el.click(function(e) {
        var filename = el.attr('href').split('/').pop();
        statistics.trackEvent('Downloads', location.pathname, filename);
    });

});
