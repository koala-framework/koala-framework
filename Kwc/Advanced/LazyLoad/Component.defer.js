var onReady = require('kwf/commonjs/on-ready');
var benchmarkBox = require('kwf/commonjs/benchmark/box');
var getKwcRenderUrl = require('kwf/commonjs/get-kwc-render-url');

onReady.onRender('.kwcClass', function(el) {
    $.ajax({
        url: getKwcRenderUrl(),
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
