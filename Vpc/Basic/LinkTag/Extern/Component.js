Vps.onContentReady(function() {
    Vps.Basic.LinkTag.Extern.processLinks();
});
Vps.Basic.LinkTag.Extern.processLinks = function(root) {
    // links holen und durchgehen
    var lnks = Ext.query('a', root || document);
    Ext.each(lnks, function(lnk) {
        // rels von link durchgehen
        lnk = Ext.get(lnk);
        var rels = lnk.dom.rel.split(' ');
        Ext.each(rels, function(rel) {
            if (rel.match(/^popup/)) {
                var relProperties = rel.split('_');
                lnk.addClass('webLinkPopup');
                lnk.on('click', function(e) {
                    e.stopEvent();
                    if (relProperties[1] == 'blank') {
                        window.open(lnk.dom.href, '_blank');
                    } else {
                        window.open(lnk.dom.href, '_blank', relProperties[1]);
                    }
                });
            }
        });
    });
};
