Ext.onReady(function()
{
    // links holen und durchgehen
    var lnks = Ext.query('a');
    Ext.each(lnks, function(lnk) {
        // rels von link durchgehen
        lnk = Ext.get(lnk);
        var rels = lnk.dom.rel.split(' ');
        Ext.each(rels, function(rel) {
            if (rel.match(/^popup/)) {
                var relProperties = rel.split('_');
                lnk.on('click', function(e) {
                    e.stopEvent();
                    window.open(lnk.dom.href, '_blank', relProperties[1]);
                });
            }
        });
    });
});
