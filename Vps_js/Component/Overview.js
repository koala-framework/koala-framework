Vps.Component.Overview = Ext.extend(Vps.Auto.GridPanel, {
    initComponent : function() {
        Vps.Component.Overview.superclass.initComponent.call(this);
        this.on('selectionchange', function() {
            if (this.getSelected()) {
                this.getAction('createTpl').enable();
                this.getAction('createCss').enable();
            } else {
                this.getAction('createTpl').disable();
                this.getAction('createCss').disable();
            }
        }, this);
    },
    getAction : function(type)
    {
        if (this.actions[type]) return this.actions[type];

        if (type == 'createTpl') {
            this.actions[type] = new Ext.Action({
                text    : 'create tpl',
                icon    : '/assets/silkicons/page_copy.png',
                cls     : 'x-btn-text-icon',
                disabled: true,
                handler : this.onCreate.createDelegate(this,
                                [ 'tpl' ]),
                scope   : this
            });
        } else if (type == 'createCss') {
            this.actions[type] = new Ext.Action({
                text    : 'create css',
                icon    : '/assets/silkicons/page_copy.png',
                cls     : 'x-btn-text-icon',
                disabled: true,
                handler : this.onCreate.createDelegate(this,
                                [ 'css' ]),
                scope: this
            });
        } else if (type == 'addComponent') {
            this.actions[type] = new Ext.Action({
                text    : 'add component',
                icon    : '/assets/silkicons/brick_add.png',
                cls     : 'x-btn-text-icon',
                handler : this.onAddComponent,
                scope: this
            });
        }
        return Vps.Component.Overview.superclass.getAction.call(this, type);
    },

    onCreate : function(createType)
    {
        if (!this.getSelected()) return;
        Ext.getBody().mask(trlVps('Copying...'));
        Ext.Ajax.request({
            url: this.controllerUrl+'/json-create',
            params: { type: createType, 'class': this.getSelected().data['class'] },
            success: function(a, b, r) {
                this.reload();
                Ext.Msg.alert(trlVps('create')+' '+createType,
                              trlVps("File successfully created:")+" "+r.path);
            },
            scope: this,
            callback: function() {
                Ext.getBody().unmask();
            }
        });
    },
    onAddComponent : function()
    {
        var data = [];
        this.metaData.components.each(function(c) {
            data.push([c, c]);
        }, this);
        var component = new Vps.Form.ComboBox({
            fieldLabel: trlVps('Component'),
            editable: false,
            triggerAction: 'all',
            forceSelection: true,
            allowBlank: false,
            width: 300,
            store: {
                data: data
            }
        });
        var name = new Ext.form.TextField({
            width: 300,
            fieldLabel: 'Name',
            allowBlank: false
        });
        component.on('select', function(cmb, record, index) {
            var r = this.getStore().getAt(0);
            if (r) {
                var m = r.data['class'].match(/^(Vpc_[A-Za-z0-9]+_)/);
                name.setValue(record.data.id.replace('Vpc_', m[1]));
            }
        }, this);
        var dlg = new Ext.Window({
            title: trlVps('Add Component'),
            width: 450,
            modal: true,
            items: [{
                xtype: 'form',
                plain: true,
                baseCls: 'x-plain',
                bodyStyle: 'padding: 10px',
                items: [component, name]
            }],
            buttons: [{
                text: 'OK',
                handler: function() {
                    dlg.close();
                    Ext.getBody().mask(trlVps('Creating...'));
                    Ext.Ajax.request({
                        url: this.controllerUrl+'/json-add-component',
                        params: {
                            'class': component.getValue(),
                            name: name.getValue()
                        },
                        success: function(a,b,r) {
                                Ext.Msg.alert(trlVps('Add Component'),
                                        trlVps("File successfully created: ")+r.path);
                        },
                        callback: function() {
                            Ext.getBody().unmask();
                        },
                        scope: this
                    });
                },
                scope: this
            },{
                text: 'Cancel',
                handler: function() {
                    dlg.close();
                }
            }]
        });
        dlg.show();
    }
});
