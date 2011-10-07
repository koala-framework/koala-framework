Ext.namespace("Vps.ComponentAjax");
Vps.ComponentAjax.ComponentAjax = (function(link, config) {
    return {
        version: '1.0',
        opts: {},
        defaults: {
            easing: 'easeNone',
            hideFx: 'fadeOut',
            showFx: 'fadeIn',
            contentClass: null, // Css-Klasse von Element, das ersetzt wird (wenn null, wird innerContent ersetzt)
            componentId: null, // ComponentId, die gerendert wird (wenn null, wird gesamte Page gerendert)
            hideFxConfig: {
                slideDirection: 'l'
            },
            showFxConfig: {
                slideDirection: 'r'
            }
        },
        
        // ************* open *****************
        open: function(link, options) {

            // Zwischen content-div und Kindknoten ein div "vpsComponentAjax" 
            // einziehen, das wird das gefadet, ansonsten geht sowas wie float:left verloren
            var content = Ext.get('vpsComponentAjax');
            if (!content) {
                if (options.contentClass) {
                    var f = Ext.query('div.' + options.contentClass);
                    var outerContent = f[0];
                } else {
                    var outerContent = Ext.get('innerContent').dom;
                }
                var content = document.createElement('div');
                content.id = 'vpsComponentAjax';
                while (outerContent.hasChildNodes()){
                    content.appendChild(outerContent.firstChild);
                }
                outerContent.appendChild(content);
                
                content = Ext.get('vpsComponentAjax');
            }
            
            Ext.applyIf(options, this.defaults);
            if (options.hideFx == 'slideOut') {
                content.slideOut(options.hideFxConfig.slideDirection, options.hideFxConfig);
            } else {
                content.fadeOut(options.hideFxConfig);
            }

            var params = {url: link.dom.href};
            if (options.componentId) {
                params.componentId = options.componentId;
            }

            Ext.Ajax.request({
                params: params,
                url: '/vps/util/render/render',
                success: function(response) {
                    content.hide();
                    content.update(response.responseText);
                    if (options.hideFx == 'slideIn') {
                        content.slideIn(options.showFxConfig.slideDirection, options.showFxConfig);
                    } else {
                        content.fadeIn(options.showFxConfig);
                    }
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