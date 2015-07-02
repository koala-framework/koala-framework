var onReady = require('kwf/on-ready');

onReady.onContentReady(function kwcPreviewLink(el) {
    if (location.search.match(/[\?&]kwcPreview/)) {
        $('a').each(function() {
            if (this.href.indexOf(window.location.host) !== -1) { // intern
                var separator = '?';
                var link = this.href;
                if (link.indexOf('?') !== -1) separator = '&';
                if (link.indexOf('kwcPreview') === -1) link += separator + 'kwcPreview';
                this.href = link;
            }
        }, this);
    }
}, { priority: -10 });
// priority because this code has to be load before every element uses a link in preview mode
