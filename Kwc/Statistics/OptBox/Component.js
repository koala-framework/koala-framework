Kwf.onContentReady(function(body, param) {
    if (!param.newRender) return;
    if (Kwf.Statistics.optBoxHtml && !Kwf.Statistics.issetUserOptValue() && !$('body').data().optbox) {
        $('body').prepend(Kwf.Statistics.optBoxHtml);
        $('body').data('optbox', true);
    }
}, this, {priority: -2}); // before Kwf.Utils.ResponsiveEl

Kwf.onJElementReady('.kwcStatisticsOptBox a.accept', function(el) {
    el.on('click', function(e, el) {
        e.preventDefault();
        Kwf.Statistics.setUserOptValue('in');
        Kwf.fireComponentEvent('cookieOptChanged', 'in');
    });
}, {priority: 10});

Kwf.onJElementReady('.kwcStatisticsOptBox a.decline', function(el) {
    el.on('click', function(e, el) {
        e.preventDefault();
        Kwf.Statistics.setUserOptValue('out');
        Kwf.fireComponentEvent('cookieOptChanged', 'out');
    });
}, {priority: 10});

Kwf.onComponentEvent('cookieOptChanged', function(value) {
    if (Kwf.Statistics.reloadOnOptChanged) {
        document.location.reload();
    }
});

