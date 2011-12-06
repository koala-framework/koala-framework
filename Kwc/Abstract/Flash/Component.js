Kwf.onContentReady(function(el, options) {
    (function(el) {
        var flashComponents = Ext.DomQuery.select('div.kwcAbstractFlash');
        Ext.each(flashComponents, function(flashComponent, el) {
            if (!flashComponent.so && Ext.get(flashComponent).isVisible(true)) {
                var inputEl = Ext.get(flashComponent).down('input');
                if (inputEl) {
                    var cfg = Ext.decode(inputEl.dom.value);
                    var flashWrapper = Ext.get(flashComponent).down('.flashWrapper');
                    if (flashWrapper) {
                        params = Ext.apply(
                            cfg.data.params,
                            {
                                'quality' : 'high',
                                'wmode' : 'opaque',
                                'allowscriptaccess' : 'always'
                            }
                        );

                        flashComponent.so = new swfobject.embedSWF(
                            cfg.data.url, //url
                            flashWrapper.id, //dom
                            cfg.data.width, cfg.data.height,
                            "9", //min version
                            "#FFFFFF",
                            cfg.vars,
                            params
                        );
                    }
                }
            }
        });
    }).defer(1, this, [el]);
});
