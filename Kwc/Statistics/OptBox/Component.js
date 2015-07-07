var onReady = require('kwf/on-ready');
var componentEvent = require('kwf/component-event');

onReady.onContentReady(function statisticsOptBox(body, param) {
    if (!param.newRender) return;
    if (Kwf.Statistics.optBoxHtml && !Kwf.Statistics.issetUserOptValue() && !$('body').data().optbox) {
        var optBox = $(Kwf.Statistics.optBoxHtml);
        $('body').prepend(optBox);
        onReady.callOnContentReady(optBox, { action: 'render' });
        $('body').data('optbox', true);
        optBox.find('a.accept').click(function(e) {
            e.preventDefault();
            Kwf.Statistics.setUserOptValue('in');
            componentEvent.trigger('cookieOptChanged', 'in');
        });
        optBox.find('a.decline').click(function(e) {
            e.preventDefault();
            Kwf.Statistics.setUserOptValue('out');
            componentEvent.trigger('cookieOptChanged', 'out');
        });
    }
}, {priority: -2}); // before ResponsiveEl


componentEvent.on('cookieOptChanged', function(value) {
    $('body').find('.cssClass').hide();
});

