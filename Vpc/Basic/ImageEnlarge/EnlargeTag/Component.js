Vps.onContentReady(function() {
    Ext.select('.vpcEnlargeTag').each(function(el) {
        if (!el.child('.webZoom')) {
            Ext.DomHelper.append(el,
                { tag: 'span', cls: 'webZoom' }
            );
        }
    });
});

