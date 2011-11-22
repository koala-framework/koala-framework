Kwf.Component.ClearCache = Ext.extend(Ext.Panel, {
    bodyStyle: 'padding: 50px;',
    initComponent: function() {
        Kwf.Component.ClearCache.superclass.initComponent.call(this);
    },
    afterRender: function() {
        Kwf.Component.ClearCache.superclass.afterRender.apply(this, arguments);
        this.body.createChild({
            style: 'font-size: 12px; margin-bottom: 20px;',
            html: '<h1>Manually Clear Cache</h1>It should not be necessary to clear the cache manually, you can still do it here'
        });

        this.body.createChild({
            style: 'font-size: 12px',
            html: 'Content changed in the CMS isn\'t shown on the website'
        });
        new Ext.Button({
            text: 'Clear View Cache',
            icon: '/assets/silkicons/page_white_text.png',
            cls: 'x-btn-text-icon',
            renderTo: this.body,
            style: 'margin-bottom: 20px',
            scope: this,
            handler: function() {
                Ext.Ajax.request({
                    url: this.controllerUrl+'/json-clear-cache',
                    params: { type: 'view' },
                    mask: true,
                    maskText: trlKwf('clearing cache...')
                });
            }
        });

        this.body.createChild({
            style: 'font-size: 12px',
            html: 'Images or other uploads aren\'t updated or shown in the wrong dimension'
        });
        new Ext.Button({
            text: 'Clear Media Cache',
            icon: '/assets/silkicons/image.png',
            cls: 'x-btn-text-icon',
            renderTo: this.body,
            style: 'margin-bottom: 20px',
            scope: this,
            handler: function() {
                Ext.Ajax.request({
                    url: this.controllerUrl+'/json-clear-cache',
                    params: { type: 'media' },
                    mask: true,
                    maskText: trlKwf('clearing cache...')
                });
            }
        });

        this.body.createChild({
            style: 'font-size: 12px',
            html: 'A Css or JavaScript file got changed but the change isn\'t visible'
        });
        new Ext.Button({
            text: 'Clear Assets Cache',
            icon: '/assets/silkicons/script_code.png',
            cls: 'x-btn-text-icon',
            renderTo: this.body,
            style: 'margin-bottom: 20px',
            scope: this,
            handler: function() {
                Ext.Ajax.request({
                    url: this.controllerUrl+'/json-clear-cache',
                    params: { type: 'assets' },
                    mask: true,
                    maskText: trlKwf('clearing cache...')
                });
            }
        });
        new Kwf.Auto.FormPanel({
            title: 'Yep, it was a cache issue, inform developers',
            border: true,
            controllerUrl: this.controllerUrl,
            renderTo: this.body
        });
    }
});
Ext.reg('kwf.component.clearCache', Kwf.Component.ClearCache);
