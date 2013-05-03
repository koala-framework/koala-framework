Kwf.onContentReady(function(el) {
    if (getParam('preview')) {
        Ext.get(el).select('a', true).each(function(a) {
            if (a.dom.href.indexOf(window.location.host) !== -1) { // intern
                var separator = '?';
                var link = a.dom.href;
                if (link.indexOf('?') !== -1) separator = '&';
                if (link.indexOf('preview') === -1) link += separator + 'preview';
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
}, this);
