Ext2.namespace("Kwf.ComponentAjax");
Kwf.ComponentAjax.ComponentAjax = (function(link, config) {
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

            // Zwischen content-div und Kindknoten ein div "kwfComponentAjax" 
            // einziehen, das wird das gefadet, ansonsten geht sowas wie float:left verloren
            var content = Ext2.get('kwfComponentAjax');
            if (!content) {
                if (options.contentClass) {
                    var f = Ext2.query('div.' + options.contentClass);
                    var outerContent = f[0];
                } else {
                    var outerContent = Ext2.get('innerContent').dom;
                }
                var content = document.createElement('div');
                content.id = 'kwfComponentAjax';
                while (outerContent.hasChildNodes()){
                    content.appendChild(outerContent.firstChild);
                }
                outerContent.appendChild(content);
                
                content = Ext2.get('kwfComponentAjax');
            }
            
            Ext2.applyIf(options, this.defaults);
            if (options.hideFx == 'slideOut') {
                content.slideOut(options.hideFxConfig.slideDirection, options.hideFxConfig);
            } else {
                content.fadeOut(options.hideFxConfig);
            }

            var params = {url: link.dom.href};
            if (options.componentId) {
                params.componentId = options.componentId;
            }

            Ext2.Ajax.request({
                params: params,
                url: '/kwf/util/render/render',
                success: function(response) {
                    content.hide();
                    content.update(response.responseText);
                    if (options.hideFx == 'slideIn') {
                        content.slideIn(options.showFxConfig.slideDirection, options.showFxConfig);
                    } else {
                        content.fadeIn(options.showFxConfig);
                    }
                    Kwf.callOnContentReady(content, {newRender: true});
                },
                scope: this
            });
        },
        
        check: function()
        {
            var components = Ext2.query('div.kwfComponentAjax');
            Ext2.each(components, function(c) {
                var div = Ext2.get(c);
                var settings = Ext2.decode(div.child('.settings').getValue());
                var link = div.child('.' + settings.sel);
                link.on('click', function (ev) {
                    ev.preventDefault();
                    Kwf.ComponentAjax.ComponentAjax.open(link, settings);
                });
            });
        }
    };
})();

Kwf.onContentReady(Kwf.ComponentAjax.ComponentAjax.check);