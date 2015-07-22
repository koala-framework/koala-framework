var onReady = require('kwf/on-ready');
var componentEvent = require('kwf/component-event');
var statistics = require('kwf/statistics');

onReady.onContentReady(function statisticsOptBox(body, param) {
    if (!param.newRender) return;
    if (statistics.optBoxHtml && !statistics.issetUserOptValue() && !$('body').data().optbox) {
        var optBox = $(statistics.optBoxHtml);
        $('body').prepend(optBox);
        onReady.callOnContentReady(optBox, { action: 'render' });
        $('body').data('optbox', true);
        optBox.find('a.accept').click(function(e) {
            e.preventDefault();
            statistics.setUserOptValue('in');
            componentEvent.trigger('cookieOptChanged', 'in');
        });
        optBox.find('a.decline').click(function(e) {
            e.preventDefault();
            statistics.setUserOptValue('out');
            componentEvent.trigger('cookieOptChanged', 'out');
        });
    }
}, {priority: -2}); // before ResponsiveEl


componentEvent.on('cookieOptChanged', function(value) {
    $('body').find('.kwcClass').hide();
});

