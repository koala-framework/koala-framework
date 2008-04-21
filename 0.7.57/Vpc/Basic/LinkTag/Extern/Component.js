Ext.onReady(function()
{
    // links holen und durchgehen
    var lnks = Ext.query('a');
    Ext.each(lnks, function(lnk) {
        // rels von link durchgehen
        lnk = Ext.get(lnk);
        var rels = lnk.dom.rel.split(' ');
        Ext.each(rels, function(rel) {
            if (rel.match(/forumDeleteConfirmation/)) {
                lnk.on('click', function(e) {
                    if (!confirm(trlVps("Do you really wish to delete this post?"))) {
                        e.stopEvent();
                    }
                });
            }
        });
    });
});
