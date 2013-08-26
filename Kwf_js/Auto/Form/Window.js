Ext.namespace('Kwf.Auto.Form');

Kwf.Auto.Form.Window = Ext.extend(Ext.Window, {
    saveText: trlKwf('Save'),
    cancelText: trlKwf('Cancel'),
    closeText: trlKwf('Close'),

    initComponent : function()
    {
        if (!this.editTitle) {
            this.editTitle = trlKwf('Edit');
            if (this.title) {
                this.editTitle = trlKwf('Edit {0}', this.title);
            }
        }
        if (!this.addTitle) {
            this.addTitle = trlKwf('Add');
            if (this.title) {
                this.addTitle = trlKwf('Add {0}', this.title);
            }
        }
        if (!this.autoForm) {
            if (!this.formConfig) this.formConfig = {};

            Ext.applyIf(this.formConfig, {
                baseCls: 'x-plain',
                controllerUrl: this.controllerUrl,
                autoLoad: false,
                checkDirty: false
            });
            this.autoForm = new Kwf.Auto.FormPanel(this.formConfig);
        } else if (typeof this.autoForm == 'string') {
            try {
                var d = eval(this.autoForm);
            } catch (e) {
                throw new Error("Invalid autoForm \'"+this.autoForm+"': "+e);
            }
            this.autoForm = new d({
                baseCls: 'x-plain',
                controllerUrl: this.controllerUrl,
                autoLoad: false,
                checkDirty: false
            });
        }

        var onRender = function() {
            this.getForm().waitMsgTarget = this.el;
            this.getForm().loadAfterSave = false; //dialog wird geschlossen nach speichern, ist also nicht nötig
        };
        if (!this.autoForm.rendered) {
            this.on('renderform', onRender, this);
        } else {
            onRender();
        }

        var buttons = this.getButtons();
        if (this.initialConfig['close']) {
            buttons = [this.getAction('close')];
        }
        Ext.applyIf(this, {
            layout: 'fit',
            bodyStyle:'padding:5px;',
            plain: true,
            modal: true,
            baseCls: 'x-plain',
            buttons: buttons
        });
        this.closeAction = 'hide';

        this.items = [this.autoForm];

        this.relayEvents(this.autoForm, ['renderform', 'datachange', 'beforeloadform', 'loadform', 'addaction']);

        Kwf.Auto.Form.Window.superclass.initComponent.call(this);
    },

    getButtons : function ()
    {
        return [this.getAction('save'), this.getAction('cancel')];
    },

    getAction : function(type)
    {
        if (!this.actions) { this.actions = {}; }
        if (this.actions[type]) return this.actions[type];

        if (type == 'save') {
            this.actions[type] = new Ext.Action({
                text    : this.saveText,
                handler : function() {
                    this.getAutoForm().submit({
                        success: function() {
                            this.hide();
                        },
                        scope: this
                    });
                },
                scope   : this
            });
        } else if (type == 'cancel') {
            this.actions[type] = new Ext.Action({
                text    : this.cancelText,
                handler : function() {
                    this.hide();
                },
                scope   : this
            });
        } else if (type == 'close') {
            this.actions[type] = new Ext.Action({
                text    : this.closeText,
                handler : function() {
                    this.hide();
                },
                scope   : this
            });
        } else {
            throw 'unknown action-type: ' + type;
        }
        return this.actions[type];
    },

    showAdd : function()
    {
        this.setTitle(this.addTitle);
        this.show();

        this.getAutoForm().onAdd({
            success: function() {
                if (!this.width || !this.height) {
                    this._kwfResize();
                }
            },
            scope: this
        });
    },

    showEdit : function(id, record)
    {
        this.setTitle(this.editTitle);
        this.show();

        if (id) {
            this.getAutoForm().load(id, {
                focusAfterLoad: true,
                success: function() {
                    if (!this.width || !this.height) {
                        this._kwfResize();
                    }
                },
                scope: this
            });
        }
    },

    _kwfResize : function()
    {
        var maxHeight = Math.ceil(Ext.getBody().getHeight() * 0.9);
        var maxWidth = Math.ceil(Ext.getBody().getWidth() * 0.9);

        if (this.el.getHeight() > maxHeight) {
            this.setSize(this.el.getWidth(), maxHeight);
        }
        if (this.el.getWidth() > maxWidth) {
            this.setSize(maxWidth, this.el.getHeight());
        }
        this.center();
    },

    getAutoForm : function()
    {
        return this.autoForm;
    },

    getForm : function()
    {
        return this.getAutoForm().getForm();
    },

    findField: function(f)
    {
        return this.getAutoForm().findField(f);
    },

    getBaseParams: function()
    {
        return this.getAutoForm().getBaseParams();
    },
    applyBaseParams: function(p)
    {
        this.getAutoForm().applyBaseParams(p);
    }
});
Ext.reg('kwf.autoformwindow', Kwf.Auto.Form.Window);
