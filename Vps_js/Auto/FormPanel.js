Vps.Auto.FormPanel = Ext.extend(Ext.Panel, {

    initComponent: function()
    {
        if (!this.actions) this.actions = {};

        //um scrollbars zu bekommen
        if (!this.autoScroll) this.autoScroll = true;

        if (!this.border) this.border = false;
        if (!this.formConfig) this.formConfig = {};
        if (!this.checkDirty) this.checkDirty = false;
        this.maskDisabled = false;

        this.addEvents({
            loadform: true,
            datachange: true,
            deleteaction: true,
            addaction: true,
            renderform: true
        });

        Vps.Auto.FormPanel.superclass.initComponent.call(this);

        if (!this.formConfig) this.formConfig = {};
        Ext.applyIf(this.formConfig, {
            baseParams       : {},
            trackResetOnLoad : true,
            maskDisabled     : false,
            url              : this.controllerUrl+'jsonSave'
        });

        //if (this.autoload == undefined || (this.autoload != undefined && this.autoload)) {
            this.loadForm(this.controllerUrl);
        //}
    },

    loadForm : function(controllerUrl)
    {
        this.controllerUrl = controllerUrl;
        Ext.Ajax.request({
            mask: true,
            url: this.controllerUrl+'jsonLoad',
            params: {meta: true},
            success: function(response, options, r) {
                var result = Ext.decode(response.responseText);
                this.onMetaChange(result.meta);
                if (result.data) {
                    this.fireEvent('loadform', this.getForm());
                    this.getForm().clearInvalid();
                    this.getForm().setValues(result.data);
                }
            },
            scope: this
        });
    },

    onMetaChange : function(meta)
    {
        Ext.applyIf(meta.form, this.formConfig);

        if (this.baseCls) meta.form.baseCls = this.baseCls; //use the same
        if (meta.buttons) {
            for (var b in meta.buttons) {
                if (!meta.form.tbar) meta.form.tbar = [];
                meta.form.tbar.push(this.getAction(b));
            }
        }
        this.formPanel = new Ext.FormPanel(meta.form);
        this.formPanel.on('render', function() {
            this.fireEvent('renderform', this.getForm());
        }, this);
        this.add(this.formPanel);
        this.doLayout();
        this.getForm().baseParams = {};
    },

    getAction : function(type)
    {
        if (this.actions[type]) return this.actions[type];

        if (type == 'save') {
            this.actions[type] = new Ext.Action({
                text    : 'Save',
                icon    : '/assets/vps/images/silkicons/table_save.png',
                cls     : 'x-btn-text-icon',
                handler : this.onSubmit,
                scope   : this
            });
        } else if (type == 'delete') {
            this.actions[type] = new Ext.Action({
                text    : 'Delete',
                icon    : '/assets/vps/images/silkicons/table_delete.png',
                cls     : 'x-btn-text-icon',
                handler : this.onDelete,
                scope   : this
            });
        } else if (type == 'add') {
            this.actions[type] = new Ext.Action({
                text    : 'New Entry',
                icon    : '/assets/vps/images/silkicons/table_add.png',
                cls     : 'x-btn-text-icon',
                handler : this.onAdd,
                scope   : this
            });
        } else {
            throw "unknown action-type: "+type;
        }
        return this.actions[type];
    },

    load : function(id, options) {
        this.getForm().baseParams.id = id;
        if (!options) options = {};
        this.getForm().clearValues();
        this.getForm().clearInvalid();
        this.getForm().waitMsgTarget = this.el;
        this.getForm().load(Ext.applyIf(options, {
            url: this.controllerUrl+'jsonLoad',
            waitMsg: 'Loading...',
            success: function(form, action) {
                if (this.actions['delete']) this.actions['delete'].enable();
                this.fireEvent('loadform', this.getForm());
            },
            scope: this
        }));
    },

    mabySave : function(callback, callCallbackIfNotDirty)
    {
        if(typeof callCallbackIfNotDirty == 'undefined') callCallbackIfNotDirty = true;
        if (this.checkDirty && this.getForm().isDirty()) {
            Ext.Msg.show({
            title:'speichern?',
            msg: 'Möchten Sie diesen Eintrag speichern?',
            buttons: Ext.Msg.YESNOCANCEL,
            scope: this,
            fn: function(button) {
                if (button == 'yes') {
                    this.onSubmit({}, callback);
                } else if (button == 'no') {
                    Ext.callback(callback.callback, callback.scope);
                } else if (button == 'cancel') {
                    //nothing to do, action allread canceled
                }
            }});
            return false;
        } else if (callCallbackIfNotDirty) {
            callback.callback.apply(callback.scope);
        }
        return true;
    },
    onSubmit : function(options, successCallback) {
        this.getAction('save').disable();

        if (!options) options = {};
        this.getForm().waitMsgTarget = this.el;
        this.getForm().submit(Ext.applyIf(options, {
            waitMsg: 'speichern...',
            success: function(form, action) {
                this.onSubmitSuccess(form, action);
                if (successCallback) {
                    Ext.callback(successCallback.callback, successCallback.scope);
                }
            },
            failure: this.onSubmitFailure,
            scope: this
        }));
    },
    onSubmitFailure: function(form, action) {
        if(action.failureType == Ext.form.Action.CLIENT_INVALID) {
            Ext.Msg.alert('Speichern', 'Es konnte nicht gespeichert werden, bitte alle Felder korrekt ausfüllen.');
        }
        this.getAction('save').enable();
    },

    onSubmitSuccess: function(form, action) {
        this.getForm().resetDirty();
        this.fireEvent('datachange', action.result);

        var reEnableSubmitButton = function() {
            this.getAction('save').enable();
        };
        reEnableSubmitButton.defer(1000, this);

        if(action.result && action.result.data && action.result.data.addedId) {
            this.getForm().baseParams.id = action.result.data.addedId;
            this.getAction('delete').enable();
            this.getAction('save').enable();
        }
        if (this.getForm().loadAfterSave && !(this instanceof Vps.Auto.Form.Window)) {
            this.load();
        }
    },
    onDelete : function() {
        Ext.Msg.show({
        title:'löschen?',
        msg: 'Möchten Sie diesen Eintrag wirklich löschen?',
        buttons: Ext.Msg.YESNO,
        scope: this,
        fn: function(button) {
            if (button == 'yes') {

                Ext.Ajax.request({
                        url: this.controllerUrl+'jsonDelete',
                        params: {id: this.getForm().baseParams.id},
                        success: function(response, options, r) {
                            this.fireEvent('datachange', r);
                            this.getForm().clearValues();
                            this.getForm().clearInvalid();
                            this.disable();
                            this.fireEvent('deleteaction', this);
                        },
                        scope: this
                    });
            }
        }
        });
    },
    onAdd : function() {
        this.mabySave({
            callback: function() {
                this.enable();
                if (this.deleteButton) this.deleteButton.disable();
                this.getAction('delete').disable();
                this.getForm().baseParams.id = 0;
                this.getForm().setDefaultValues();
                this.getForm().clearInvalid();
                this.fireEvent('addaction', this);
            },
            scope: this
        });
    },
    findField: function(id) {
        return this.getForm().findField(id);
    },

    disable : function() {
        this.getAction('save').disable();
        this.getAction('delete').disable();
        this.getForm().items.each(function(b) {
            b.disable();
        });
        Vps.Auto.FormPanel.superclass.disable.call(this);
    },
    enable : function() {
        for (var i in this.actions) {
            this.actions[i].enable();
        }
        this.getForm().items.each(function(b) {
            b.enable();
        });
        Vps.Auto.FormPanel.superclass.enable.call(this);
    },
    getForm : function() {
        return this.getFormPanel().getForm();
    },
    getFormPanel : function() {
        return this.formPanel;
    }
});
