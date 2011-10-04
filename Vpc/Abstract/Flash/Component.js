
Vps.onContentReady(function() {
    var flashComponents = Ext.DomQuery.select('div.vpcAbstractFlash');
    Ext.each(flashComponents, function(flashComponent) {
        var inputEl = Ext.get(flashComponent).down('input');
        if (inputEl) {
            var cfg = Ext.decode(inputEl.dom.value);
            var flashWrapper = Ext.get(flashComponent).down('.flashWrapper');
            if (flashWrapper) {
                var flashVars = '';
                for (var i in cfg.vars) {
                    flashVars = flashVars + '&'+i+'='+cfg.vars[i];
                }

                if (flashVars.length >= 1) {
                    flashVars = flashVars.substr(1); // erstes zeichen wegschneiden
                }

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
                    flashVars,
                    params
                );
            }
        }
    });
});
