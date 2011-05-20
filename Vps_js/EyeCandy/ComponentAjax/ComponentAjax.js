Ext.namespace("Vps.ComponentAjax");
Vps.ComponentAjax.ComponentAjax = (function(link, config) {
    return {
        version: '1.0',
        opts: {},
        defaults: {
            easing: 'easeNone',
            hideFx: 'fadeOut',
            showFx: 'fadeIn',
            hideFxConfig: {
                slideDirection: 'l'
            },
            showFxConfig: {
                slideDirection: 'r'
            }
        },
        
        // ************* open *****************
        open: function(link, options) {
            var content = Ext.get('vpsComponentAjax');

            // Zwischen innerContent-div und Kindknote ein div "vpsComponentAjax" 
            //einziehen, das wird das gefadet, ansonsten geht sowas wie float:left verloren
            if (!content) { 
                var innerContent = Ext.get('innerContent').dom;
                var content = document.createElement('div');
                content.id = 'vpsComponentAjax';
                while (innerContent.hasChildNodes()){
                    content.appendChild(innerContent.firstChild);
                }
                innerContent.appendChild(content);
                content = Ext.get('vpsComponentAjax');
            }
            
            Ext.applyIf(options, this.defaults);
            if (options.hideFx == 'slideOut') {
                content.slideOut(options.hideFxConfig.slideDirection, options.hideFxConfig);
            } else if (options.hideFx == 'fadeOut') {
                content.fadeOut(options.hideFxConfig);
            }
            Ext.Ajax.request({
                params: {url: link.dom.href},
                url: '/vps/util/render/render',
                success: function(response) {
                    content.update(response.responseText);
                    content.slideIn(options.showFxConfig.slideDirection, options.showFxConfig);
                    Vps.callOnContentReady();
                },
                scope: this
            });
        },
        
        check: function()
        {
            var components = Ext.query('div.vpsComponentAjax');
            Ext.each(components, function(c) {
                var div = Ext.get(c);
                var settings = Ext.decode(div.child('.settings').getValue());
                var link = div.child('.' + settings.sel);
                link.on('click', function (ev) {
                    ev.preventDefault();
                    Vps.ComponentAjax.ComponentAjax.open(link, settings);
                });
            });
        }
    };
})();

Vps.onContentReady(Vps.ComponentAjax.ComponentAjax.check);