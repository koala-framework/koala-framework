Kwf.onContentReady(function kwcPreviewLink(el) {
    if (location.search.match(/[\?&]kwcPreview/)) {
        Ext2.get(el).select('a', true).each(function(a) {
            if (a.dom.href.indexOf(window.location.host) !== -1) { // intern
                var separator = '?';
                var link = a.dom.href;
                if (link.indexOf('?') !== -1) separator = '&';
                if (link.indexOf('kwcPreview') === -1) link += separator + 'kwcPreview';
                a.set({ href: link });
            }
        }, this);
    }
}, { priority: -10 });
// priority because this code has to be load before every element uses a link in preview mode
