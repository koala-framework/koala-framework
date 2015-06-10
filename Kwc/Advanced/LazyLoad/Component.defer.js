Kwf.onJElementReady('.cssClass', function(el) {
    $.ajax({
        url: Kwf.getKwcRenderUrl(),
        data: {
            componentId: el.attr('data-load-id')
        },
        success: function(data) {
            el.removeClass('loading');
            el.find('.content').html(data);
            var t = Kwf.Utils.BenchmarkBox.now();
            Kwf.callOnContentReady(el.find('.content'), { action: 'render' });
            Kwf.Utils.BenchmarkBox.time('time', Kwf.Utils.BenchmarkBox.now()-t);
            Kwf.Utils.BenchmarkBox.create({
                counters: Kwf._onReadyStats,
                type: 'onReady lazyLoad'
            });
        },
        dataType: 'html'
    });
});
