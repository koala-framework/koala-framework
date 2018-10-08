var onReady = require('kwf/on-ready');
var benchmarkBox = require('kwf/benchmark/box');
var KwfBaseUrl = require('kwf/base-url');
var getKwcRenderUrl = require('kwf/get-kwc-render-url');

onReady.onRender('.kwcClass', function(el) {
    var baseUrl = KwfBaseUrl.get();
    $.ajax({
        url: baseUrl + getKwcRenderUrl(),
        data: {
            componentId: el.attr('data-load-id'),
            pageUrl: location.href
        },
        success: function(data) {
            el.removeClass('loading');
            el.find('.content').html(data);
            var t = benchmarkBox.now();
            onReady.callOnContentReady(el.find('.content'), { action: 'render' });
            benchmarkBox.time('time', benchmarkBox.now()-t);
            benchmarkBox.create({
                type: 'onReady lazyLoad'
            });
        },
        dataType: 'html'
    });
});
