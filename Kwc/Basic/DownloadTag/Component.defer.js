var onReady = require('kwf/on-ready');
var statistics = require('kwf/statistics');

onReady.onRender('.kwcClass', function (el, config) {

    el.click(function(e) {
        var filename = el.attr('href').split('/').pop();
        statistics.trackEvent('Downloads', location.pathname, filename);
    });

});
