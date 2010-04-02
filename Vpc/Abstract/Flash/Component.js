
Vps.onContentReady(function() {
    var flashComponents = Ext.DomQuery.select('div.vpcAbstractFlash');
    Ext.each(flashComponents, function(flashComponent) {
        var inputEl = Ext.get(flashComponent).down('input');
        if (inputEl) {
            var cfg = Ext.decode(inputEl.dom.value);
            var flashWrapper = Ext.get(flashComponent).down('.flashWrapper');

            var flashVars = '';
            for (var i in cfg.vars) {
                flashVars = flashVars + '&'+i+'='+cfg.vars[i];
            }

            if (flashVars.length >= 1) {
                flashVars = flashVars.substr(1); // erstes zeichen wegschneiden
            }

            var so = new SWFObject(
                cfg.data.url,
                "ply"+flashWrapper.id,
                cfg.data.width, cfg.data.height,
                "9", "#FFFFFF"
            );

            so.addParam("quality", "high");
            so.addParam("wmode", "opaque");
            so.addParam("allowscriptaccess", "always");
            for (var i in cfg.data.params) {
                so.addParam(i, cfg.data.params[i]);
            }

            so.addParam("flashVars", flashVars);
            so.write(flashWrapper.id);
        }
    });
});
