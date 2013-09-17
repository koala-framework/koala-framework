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

        this.formPanel = new Ext.FormPanel({
            renderTo: this.body,
            baseCls: 'x-plain',
            labelWidth: 180,
            items: [new Ext.form.FieldSet({
                title: 'Filter',
                autoHeight: true,
                width: 400,
                items: [
                    {
                        style: 'font-size: 12px; ',
                        html: 'Use % as placeholder',
                        border: false
                    },
                    new Ext.form.TextField({
                        fieldLabel: 'component_id',
                        name: 'id'
                    }),
                    new Ext.form.TextField({
                        fieldLabel: 'db_id',
                        name: 'dbId'
                    }),
                    new Ext.form.TextField({
                        fieldLabel: 'expanded_component_id',
                        name: 'expandedId'
                    }),
                    new Ext.form.TextField({
                        fieldLabel: 'type',
                        name: 'type'
                    }),
                    new Ext.form.TextField({
                        fieldLabel: 'component_class',
                        name: 'class'
                    })
                ]
            })]
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
                    url: this.controllerUrl+'/json-clear-view-cache',
                    params: this.formPanel.getForm().getValues(),
                    mask: true,
                    maskText: trlKwf('clearing cache...'),
                    success: function(response, options, r) {
                        Ext.Msg.show({
                            title:trlKwf('Clear Cache'),
                            msg: 'This will clear '+r.entries+' view cache entries. Continue?',
                            buttons: Ext.Msg.OKCANCEL,
                            scope: this,
                            fn: function(button) {
                                if (button == 'ok') {
                                    var params = this.formPanel.getForm().getValues();
                                    params.force = true;
                                    Ext.Ajax.request({
                                        url: this.controllerUrl+'/json-clear-view-cache',
                                        params: params,
                                        mask: true,
                                        maskText: trlKwf('clearing cache...')
                                    });
                                }
                            }});

                    },
                    scope: this
                });
            }
        });

        this.body.createChild({
            style: 'font-size: 12px',
            html: 'Changed translations aren\'t updated'
        });
        new Ext.Button({
            text: 'Clear Trl Cache',
            icon: '/assets/silkicons/application_view_columns.png',
            cls: 'x-btn-text-icon',
            renderTo: this.body,
            style: 'margin-bottom: 20px',
            scope: this,
            handler: function() {
                Ext.Ajax.request({
                    url: this.controllerUrl+'/json-clear-cache',
                    params: { type: 'trl' },
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
