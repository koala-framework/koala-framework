Vps.Auto.FormPanel = Ext.extend(Ext.Panel, {

    controllerUrl: '',
    actions : {},

//     checkDirty: false,

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
        add: true
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
                this.form.clearInvalid();
                this.form.setValues(result.data);
            },
            scope: this
        });
    },

    onMetaChange : function(meta)
    {
        this.form = new Ext.FormPanel(meta.form);
        this.add(this.form);
        this.doLayout();
    },

    getAction : function(type)
    {
        if (this.actions[type]) return this.actions[type];

        if (type == 'save') {
            this.actions[type] = new Ext.Action({
                id      : 'save',
                text    : 'Save',
                icon    : '/assets/vps/images/silkicons/table_save.png',
                cls     : 'x-btn-text-icon',
                handler : this.onSubmit,
                scope   : this
            });
        } else if (type == 'delete') {
            this.actions[type] = new Ext.Action({
                id      : 'delete',
                text    : 'Delete',
                icon    : '/assets/vps/images/silkicons/table_delete.png',
                cls     : 'x-btn-text-icon',
                handler : this.onDelete,
                scope   : this
            });
        } else if (type == 'add') {
            this.actions[type] = new Ext.Action({
                id      : 'add',
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
        this.form.baseParams.id = id;
        if (!options) options = {};
        this.form.getForm().clearValues();
        this.form.getForm().clearInvalid();
        this.form.load(Ext.applyIf(options, {
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
        if (this.checkDirty && this.form.isDirty()) {
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
        if (this.actions.save) this.actions.save.disable();

        if (!options) options = {};
        this.form.submit(Ext.applyIf(options, {
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
                if (this.actions.save) this.actions.save.enable();
            },
            scope: this
        }));
    },
    onSubmitSuccess: function(form, action) {
        this.form.resetDirty();
        this.fireEvent('dataChanged', action.result);

        var reEnableSubmitButton = function() {
            if (this.actions.save) this.actions.save.enable();
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
                        params: {id: this.form.baseParams.id},
                        success: function(response, options, r) {
                            this.fireEvent('dataChanged', r);
                            this.form.clearValues();
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
                if (this.actions['delete']) this.actions['delete'].disable();
                this.form.baseParams.id = 0;
                this.form.setDefaultValues();
                this.form.clearInvalid();
                this.fireEvent('add', this);
            },
            scope: this
        });
    },
    findField: function(id) {
        return this.form.findField(id);
    },
    disable : function() {
        if (this.actions.save) this.actions.save.disable();
        if (this.actions['delete']) this.actions['delete'].disable();
        this.form.items.each(function(b) {
            b.disable();
        });
    },
    enable : function() {
        for (var i in this.actions) {
            this.actions[i].enable();
        }
        this.form.items.each(function(b) {
            b.enable();
        });
    }
});
