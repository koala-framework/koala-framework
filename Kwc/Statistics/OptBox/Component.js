Kwf.onContentReady(function statisticsOptBox(body, param) {
    if (!param.newRender) return;
    if (Kwf.Statistics.defaultOptValue == 'out' && Kwf.Statistics.optBoxHtml
        && !Kwf.Statistics.issetUserOptValue() && !$('body').data().optbox
    ) {
        $('body').prepend(Kwf.Statistics.optBoxHtml);
        $('body').data('optbox', true);
        Kwf.callOnContentReady(document.body, { action: 'render' });
    }
}, {priority: -2}); // before Kwf.Utils.ResponsiveEl

Kwf.onElementReady('.kwcStatisticsOptBox a.accept', function statisticsOptBox(link) {
    link.on('click', function(e, el) {
        e.preventDefault();
        e.stopEvent();
        Kwf.Statistics.setUserOptValue('in');
        Kwf.fireComponentEvent('cookieOptChanged', 'in');
        return false;
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
    $('body').find('.cssClass').hide();
    if (Kwf.Statistics.reloadOnOptChanged) {
        document.location.reload();
    }
});

