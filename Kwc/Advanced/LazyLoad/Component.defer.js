var onReady = require('kwf/on-ready');
var benchmarkBox = require('kwf/benchmark/box');
var getKwcRenderUrl = require('kwf/get-kwc-render-url');

onReady.onRender('.cssClass', function(el) {
    $.ajax({
        url: getKwcRenderUrl(),
        data: {
            componentId: el.attr('data-load-id')
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
