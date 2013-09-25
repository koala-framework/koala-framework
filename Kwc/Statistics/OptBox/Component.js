Kwf.onContentReady(function(body, param) {
    if (!param.newRender) return;
    // TODO: make default behaviour customizable
    if (Kwf.Statistics.getDefaultOptValue() == 'out' && !Kwf.Statistics.issetUserOptValue()) {
        if (Ext.getBody().optBox) return;
        var html = '<div class="' + Kwf.Statistics.cssClass + '">';
        html += '<div class="inner">';
        html += trlKwf('This website uses cookies to help us give you the best experience when you visit our website.');
        if (Kwf.Statistics.optUrl) {
            html += ' <a href="' + Kwf.Statistics.optUrl + '" class="info">' + trlKwf('More information about the use of cookies') + '</a>';
        }
        html += '<a href="" class="accept"><span>' + trlKwf('Accept and continue') + '</span></a>';
        html += '<div></div>';
        Ext.getBody().insertFirst(html);
        Ext.getBody().optBox = true;
    }
}, this, {priority: -2}); // before Kwf.Utils.ResponsiveEl

Kwf.onElementReady('.kwcStatisticsOptBox a.accept', function(link) {
    link.on('click', function(e, el) {
        e.stopEvent();
        Kwf.Statistics.setUserOptValue('in');
        Kwf.fireComponentEvent('cookieOptChanged', 'in');
    });
}, {priority: 10});

Kwf.onComponentEvent('cookieOptChanged', function(value) {
    if (Kwf.Statistics.reloadOnOptChanged) {
        document.location.reload();
    }
});

