Kwf.onElementReady('a', function onElementReadyLinkExtern(lnk) {
        var rels = lnk.dom.rel.split(' ');
        Ext.each(rels, function(rel) {
            if (rel.match(/^popup/)) {
                var relProperties = rel.split('_');
                lnk.addClass('webLinkPopup');
                lnk.dom.kwfOpenWindow = function() { //to be able to open popup from other JS code
                    if (relProperties[1] == 'blank') {
                        window.open(lnk.dom.href, '_blank');
                    } else {
                        window.open(lnk.dom.href, '_blank', relProperties[1]);
                    }
                };
                lnk.on('click', function(e, el) {
                    e.stopEvent();
                    if (el.kwfOpenWindow) {
                        el.kwfOpenWindow();
                    } else {
                        Ext.fly(el).up('a').dom.kwfOpenWindow();
                    }
                });
            }
        });
});
