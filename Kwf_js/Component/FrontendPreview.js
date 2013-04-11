Kwf.onContentReady(function(el) {
    if (getParam('preview')==='true') {
        Ext.get(el).select('a', true).each(function(a) {
            if (a.dom.href.indexOf(window.location.host) !== -1) { // intern
                var separator = '?';
                var link = a.dom.href;
                if (link.indexOf('?') !== -1) separator = '&';
                if (link.indexOf('preview=true') === -1) link += separator + 'preview=true';
                a.set({ href: link });
            }
        }, this);
    }

    function getParam (param) {
        var query = window.location.search.substring(1);
        var params = query.split("&");
        for (var i=0; i<params.length; i++) {
            var pair = params[i].split("=");
            if (pair[0] == param) return pair[1];
        }
        return false;
    }
}, this);
