Vps.onContentReady(function() {
    Ext.query('.vpcEnlargeTag').each(function(el) {
        if (!Ext.fly(el).child('.webZoom')) {
            Ext.DomHelper.append(el,
                { tag: 'span', cls: 'webZoom' }
            );
        }
    });
});

