Vps.Auto.FormPanel = Ext.extend(Ext.Panel, {

    controllerUrl: '',
    formConfig: {},
    actions : {},

    checkDirty: false,

    initComponent: function()
    {
        //um scrollbars zu bekommen
        if (!this.autoScroll) this.autoScroll = true;
        if (!this.border) this.border = false;

//         if (!this.waitMsgTarget) this.waitMsgTarget = document.body;
//         trackResetOnLoad: true,
    this.addEvents({
        formRendered: true,
        loaded: true,
        //generatetoolbar: true,
        dataChanged: true,
        deleted: true,
        add: true,
        renderform: true
    });
        Vps.Auto.FormPanel.superclass.initComponent.call(this);

        if (!this.formConfig) this.formConfig = {};
        Ext.applyIf(this.formConfig, {
            baseParams : {},
            url : this.controllerUrl+'jsonSave'
        });

        Ext.Ajax.request({
            url: this.controllerUrl+'jsonLoad',
            params: {meta: true},
            success: function(response, options, r) {
                var result = Ext.decode(response.responseText);
                this.onMetaChange(result.meta);
                this.getForm().clearInvalid();
                this.getForm().setValues(result.data);
            },
            scope: this
        });
    },

    onMetaChange : function(meta)
    {
        Ext.applyIf(meta.form, this.formConfig);

        if (this.baseCls) meta.form.baseCls = this.baseCls; //use the same
        this.formPanel = new Ext.FormPanel(meta.form);
        this.add(this.formPanel);
        this.doLayout();
        this.getForm().baseParams = {};
        this.fireEvent('renderform', this.getForm());
        debugger;
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
        this.getForm().load(Ext.applyIf(options, {
            url: this.controllerUrl+'jsonLoad',
//             waitMsg: 'loading...',
            success: function(form, action) {
                if (this.actions['delete']) this.actions['delete'].enable();
                this.fireEvent('loaded', form, action);
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
            Ext.callback(callback.callback, callback.scope);
        }
        return true;
    },
    onSubmit : function(options, successCallback) {
        this.getAction('save').disable();

        if (!options) options = {};
        this.getForm().submit(Ext.applyIf(options, {
            waitMsg: 'speichern...',
            success: function(form, action) {
                this.onSubmitSuccess(form, action);
                if (successCallback) {
                    Ext.callback(successCallback.callback, successCallback.scope);
                }
            },
            failure: function(form, action) {
                if(action.failureType == Ext.form.Action.CLIENT_INVALID) {
                    Ext.Msg.alert('Speichern', 'Es konnte nicht gespeichert werden, bitte alle Felder korrekt ausfüllen.');
                }
                this.getAction('save').enable();
            },
            scope: this
        }));
    },
    onSubmitSuccess: function(form, action) {
        this.getForm().resetDirty();
        this.fireEvent('dataChanged', action.result);

        var reEnableSubmitButton = function() {
            this.getAction('save').enable();
        };
        reEnableSubmitButton.defer(1000, this);
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
                            this.fireEvent('dataChanged', r);
                            this.getForm().clearValues();
                            this.disable();
                            this.fireEvent('deleted', this);
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
                this.fireEvent('add', this);
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
    },
    enable : function() {
        for (var i in this.actions) {
            this.actions[i].enable();
        }
        this.getForm().items.each(function(b) {
            b.enable();
        });
    },
    getForm : function() {
        return this.getFormPanel().getForm();
    },
    getFormPanel : function() {
        return this.formPanel;
    }
});
