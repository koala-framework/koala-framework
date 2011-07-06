
Vps.onContentReady(function() {
    var flashComponents = Ext.DomQuery.select('div.vpcAbstractFlash');
    Ext.each(flashComponents, function(flashComponent) {
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

                var so = new swfobject.embedSWF(
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
    });
});
