Kwf.onContentReady(function() {
    Ext.query('.kwcBasicImageEnlarge').each(function(el) {
        if (!Ext.fly(el).hasClass('showHoverIcon')) return;
        var el = Ext.fly(el).child('a');
        if (el && !Ext.fly(el).child('.webZoom')) {
            if (el.getWidth() > 100 && el.getHeight() > 100) {
                Ext.DomHelper.append(el,
                        { tag: 'span', cls: 'outerHoverIcon', html: '<span class="innerHoverIcon"></span>'}
                );
            }
        }
    });
});

