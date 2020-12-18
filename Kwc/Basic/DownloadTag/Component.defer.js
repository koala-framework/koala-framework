var onReady = require('kwf/on-ready');
var statistics = require('kwf/statistics');
var dataLayer = require('kwf/data-layer');

onReady.onRender('.kwcClass', function (el, config) {

    el.click(function(e) {
        var filename = el.attr('href').split('/').pop();
        statistics.trackEvent('Downloads', location.pathname, filename);
        dataLayer.push({
            event: "download",
            download_filename: filename
        });
    });

});
