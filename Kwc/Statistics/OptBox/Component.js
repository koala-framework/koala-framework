var onReady = require('kwf/on-ready');

onReady.onContentReady(function statisticsOptBox(body, param) {
    if (!param.newRender) return;
    if (Kwf.Statistics.optBoxHtml && !Kwf.Statistics.issetUserOptValue() && !$('body').data().optbox) {
        var optBox = $(Kwf.Statistics.optBoxHtml);
        $('body').prepend(optBox);
        Kwf.callOnContentReady(optBox, { action: 'render' });
        $('body').data('optbox', true);
        optBox.find('a.accept').click(function(e) {
            e.preventDefault();
            Kwf.Statistics.setUserOptValue('in');
            Kwf.fireComponentEvent('cookieOptChanged', 'in');
        });
        optBox.find('a.decline').click(function(e) {
            e.preventDefault();
            Kwf.Statistics.setUserOptValue('out');
            Kwf.fireComponentEvent('cookieOptChanged', 'out');
        });
    }
}, {priority: -2}); // before ResponsiveEl


Kwf.onComponentEvent('cookieOptChanged', function(value) {
    $('body').find('.cssClass').hide();
});

