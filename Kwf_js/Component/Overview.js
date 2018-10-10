Kwf.Component.Overview = Ext2.extend(Kwf.Auto.GridPanel, {
    initComponent : function() {
        Kwf.Component.Overview.superclass.initComponent.call(this);
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
            this.actions[type] = new Ext2.Action({
                text    : 'create tpl',
                icon    : '/assets/silkicons/page_copy.png',
                cls     : 'x2-btn-text-icon',
                disabled: true,
                handler : this.onCreate.createDelegate(this,
                                [ 'tpl' ]),
                scope   : this
            });
        } else if (type == 'createCss') {
            this.actions[type] = new Ext2.Action({
                text    : 'create css',
                icon    : '/assets/silkicons/page_copy.png',
                cls     : 'x2-btn-text-icon',
                disabled: true,
                handler : this.onCreate.createDelegate(this,
                                [ 'css' ]),
                scope: this
            });
        } else if (type == 'addComponent') {
            this.actions[type] = new Ext2.Action({
                text    : 'add component',
                icon    : '/assets/silkicons/brick_add.png',
                cls     : 'x2-btn-text-icon',
                handler : this.onAddComponent,
                scope: this
            });
        }
        return Kwf.Component.Overview.superclass.getAction.call(this, type);
    },

    onCreate : function(createType)
    {
        if (!this.getSelected()) return;
        Ext2.getBody().mask(trlKwf('Copying...'));
        Ext2.Ajax.request({
            url: this.controllerUrl+'/json-create',
            params: { type: createType, 'class': this.getSelected().data['class'] },
            success: function(a, b, r) {
                this.reload();
                Ext2.Msg.alert(trlKwf('create')+' '+createType,
                              trlKwf("File successfully created:")+" "+r.path);
            },
            scope: this,
            callback: function() {
                Ext2.getBody().unmask();
            }
        });
    },
    onAddComponent : function()
    {
        var data = [];
        this.metaData.components.each(function(c) {
            data.push([c, c]);
        }, this);
        var component = new Kwf.Form.ComboBox({
            fieldLabel: trlKwf('Component'),
            editable: false,
            triggerAction: 'all',
            forceSelection: true,
            allowBlank: false,
            width: 300,
            store: {
                data: data
            }
        });
        var name = new Ext2.form.TextField({
            width: 300,
            fieldLabel: 'Name',
            allowBlank: false
        });
        component.on('select', function(cmb, record, index) {
            var r = this.getStore().getAt(0);
            if (r) {
                var m = r.data['class'].match(/^(Kwc_[A-Za-z0-9]+_)/);
                name.setValue(record.data.id.replace('Kwc_', m[1]));
            }
        }, this);
        var dlg = new Ext2.Window({
            title: trlKwf('Add Component'),
            width: 450,
            modal: true,
            items: [{
                xtype: 'form',
                plain: true,
                baseCls: 'x2-plain',
                bodyStyle: 'padding: 10px',
                items: [component, name]
            }],
            buttons: [{
                text: 'OK',
                handler: function() {
                    dlg.close();
                    Ext2.getBody().mask(trlKwf('Creating...'));
                    Ext2.Ajax.request({
                        url: this.controllerUrl+'/json-add-component',
                        params: {
                            'class': component.getValue(),
                            name: name.getValue()
                        },
                        success: function(a,b,r) {
                                Ext2.Msg.alert(trlKwf('Add Component'),
                                        trlKwf("File successfully created: ")+r.path);
                        },
                        callback: function() {
                            Ext2.getBody().unmask();
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
