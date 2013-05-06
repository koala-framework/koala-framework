Kwf.onContentReady(function(el) {
    if (getParam('kwcPreview')) {
        Ext.get(el).select('a', true).each(function(a) {
            if (a.dom.href.indexOf(window.location.host) !== -1) { // intern
                var separator = '?';
                var link = a.dom.href;
                if (link.indexOf('?') !== -1) separator = '&';
                if (link.indexOf('kwcPreview') === -1) link += separator + 'kwcPreview';
                a.set({ href: link });
            }
        }, this);
    }

    function getParam (param) {
        var query = window.location.search.substring(1);
        if (query == param) return true;
        var params = query.split("&");
        for (var i=0; i<params.length; i++) {
            if (params[i] == param) return true;
        }
        return false;
    }
}, this, { priority: -10 });
// priority because this code has to be load before every element uses a link in preview mode
