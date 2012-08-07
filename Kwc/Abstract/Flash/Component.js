Kwf.onContentReady(function(el, options) {
    var flashComponents = Ext.DomQuery.select('div.kwcAbstractFlash');
    Ext.each(flashComponents, function(flashComponent, el) {
        var fc = Ext.get(flashComponent);
        var inputEl = fc.down('input');
        if (!fc.isVisible(true) && inputEl) {
            var cfg = Ext.decode(inputEl.dom.value);
            if (cfg.removeHiddenFlash && fc.down('object')) {
                fc.down('object').remove();
                fc.createChild({cls: 'flashWrapper'});
                flashComponent.so = false;
            }
        }
    });
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

                    var flashVersion = "9";

                    flashComponent.so = new swfobject.embedSWF(
                        cfg.data.url, //url
                        flashWrapper.id, //dom
                        cfg.data.width, cfg.data.height,
                        flashVersion,
                        "#FFFFFF",
                        cfg.vars,
                        params
                    );

                    if (!swfobject.hasFlashPlayerVersion(flashVersion)) {
                        Ext.fly(flashComponent).addClass('noFlash');
                    }
                }
            }
        }
    });
}, this, {priority: 10});
