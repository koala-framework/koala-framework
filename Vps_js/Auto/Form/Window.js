Ext.namespace('Vps.Auto.Form');

Vps.Auto.Form.Window = Ext.extend(Ext.Window, {
    initComponent : function()
    {
        this.actions = {};

        if (!this.formConfig) this.formConfig = {};

        Ext.applyIf(this.formConfig, {
            baseCls: 'x-plain',
            controllerUrl: this.controllerUrl
        });
        this.autoForm = new Vps.Auto.FormPanel(this.formConfig);
        this.on('render', function() {
            this.getForm().waitMsgTarget = this.el;
        }, this);
        
        Ext.applyIf(this, {
            width: 300,
            height: 200,
            layout: 'fit',
            bodyStyle:'padding:5px;',
            plain: true,
            modal: true,
            buttons: [this.getAction('cancel'), this.getAction('save')]
        });

        this.items = [this.autoForm];

        this.relayEvents(this.autoForm, ['renderform']);
        this.addEvents({
            datachange: true
        });

        Vps.Auto.Form.Window.superclass.initComponent.call(this);
    },

    getAction : function(type)
    {
        if (this.actions[type]) return this.actions[type];

        if (type == 'save') {
            var action = this.getAutoForm().getAction('save');
            action.handler = function() {
                this.getForm().onSubmit(null, {
                    callback: function() {
                        this.hide();
                    },
                    scope: this
                });
            };
            this.actions[type] = action;
        } else if (type == 'cancel') {
            this.actions[type] = new Ext.Action({
                text    : 'Cancel',
                handler : function() {
                    this.hide();
                },
                scope   : this
            });
        } else {
            throw "unknown action-type: "+type;
        }
        return this.actions[type];
    },

    showAdd : function()
    {
        this.getAutoForm().onAdd();
        this.setTitle('add');
        this.show();
    },

    showEdit : function(id, options)
    {
        this.setTitle('edit');
        this.show();
        this.getAutoForm().load(id, options);
    },

    getAutoForm : function()
    {
        return this.autoForm;
    },

    getForm : function()
    {
        return this.getAutoForm().getForm();
    }
});
