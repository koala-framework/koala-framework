Kwf.onJElementReady('.kwcAdvancedLazyLoad', function(el) {
    $.ajax({
        url: Kwf.getKwcRenderUrl(),
        data: {
            componentId: el.attr('data-load-id')
        },
        success: function(data) {
            el.removeClass('loading');
            el.find('.content').html(data);
        },
        dataType: 'html'
    });
});
