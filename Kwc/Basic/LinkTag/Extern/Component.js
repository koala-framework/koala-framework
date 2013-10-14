Kwf.onContentReady(function() {
    Kwf.Basic.LinkTag.Extern.processLinks();
});
Kwf.Basic.LinkTag.Extern.processLinks = function(root) {
    // links holen und durchgehen
    var lnks = Ext.query('a', root || document);
    Ext.each(lnks, function(lnk) {
        // check if it's allready initialized
        if (lnk.linkTagExternInitDone) return;
        lnk.linkTagExternInitDone = true;
        lnk = Ext.get(lnk);
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
};
