Vps.Auto.FormPanel = Ext.extend(Vps.Auto.AbstractPanel, {
    autoload: true,
    autoScroll: true, //um scrollbars zu bekommen
    border: false,
    formConfig: {},
    maskDisabled: true,
    layout: 'fit',

    initComponent: function()
    {
        this.actions = {};

        this.addEvents(
            'loadform',
            'deleteaction',
            'addaction',
            'renderform'
        );

        Vps.Auto.FormPanel.superclass.initComponent.call(this);

        if (!this.formConfig) this.formConfig = {};
        Ext.applyIf(this.formConfig, {
            baseParams       : {},
            trackResetOnLoad : true,
            maskDisabled     : false,
            autoScroll       : true
        });

        if (!this.controllerUrl) {
            throw new Error('No controllerUrl specified for AutoForm.');
        }
        this.formConfig.url = this.controllerUrl + '/jsonSave';

        if (this.autoload) {
            this.load();
        }
    },

    onMetaChange : function(meta)
    {
        Ext.applyIf(meta.form, this.formConfig);

        if (this.baseCls) meta.form.baseCls = this.baseCls; //use the same

        for (var i in this.actions) {
            if (!meta.permissions[i]) {
                this.getAction(i).hide();
            }
        }

        if (meta.buttons && typeof meta.form.tbar == 'undefined') {
            for (var b in meta.buttons) {
                if (!meta.form.tbar) meta.form.tbar = [];
                meta.form.tbar.push(this.getAction(b));
            }
        }
        if (this.formPanel != undefined) {
            this.remove(this.formPanel, true);
        }

        this.formPanel = new Ext.FormPanel(meta.form);
        this.formPanel.on('render', function() {
            this.fireEvent('renderform', this.getForm());
        }, this);
        this.add(this.formPanel);
        this.doLayout();
        this.getForm().baseParams = this.baseParams;
    },

    isDirty : function() {
        return this.getForm().isDirty();
    },

    getAction : function(type)
    {
        if (this.actions[type]) return this.actions[type];

        if (type == 'save') {
            this.actions[type] = new Ext.Action({
                text    : 'Save',
                icon    : '/assets/silkicons/table_save.png',
                cls     : 'x-btn-text-icon',
                handler : function() {
                    this.onSave();
                },
                scope   : this
            });
        } else if (type == 'delete') {
            this.actions[type] = new Ext.Action({
                text    : 'Delete',
                icon    : '/assets/silkicons/table_delete.png',
                cls     : 'x-btn-text-icon',
                handler : this.onDelete,
                scope   : this
            });
        } else if (type == 'add') {
            this.actions[type] = new Ext.Action({
                text    : 'New Entry',
                icon    : '/assets/silkicons/table_add.png',
                cls     : 'x-btn-text-icon',
                handler : this.onAdd,
                scope   : this
            });
        } else {
            throw 'unknown action-type: ' + type;
        }
        return this.actions[type];
    },

    load : function(params) {
        if (this.el) this.el.mask('Loading...');

        if (!params) params = {};

        //es kann auch direkt die id übergeben werden
        if (typeof params != 'object') params = { id: params };

        if (!this.getForm()) {
            params.meta = true; //wenn noch keine form vorhanden metaDaten anfordern
        }

        if (this.getForm()) {
            this.getForm().clearValues();
            this.getForm().clearInvalid();
        }

        Ext.Ajax.request({
            mask: !this.el, //globale mask wenn kein el vorhanden
            url: this.controllerUrl+'/jsonLoad',
            params: Ext.apply(this.baseParams || {}, params),
            success: function(response, options, result) {
                if (result.meta) {
                    this.onMetaChange(result.meta);
                }
                if (result.data && this.getForm()) {
                    if (this.actions['delete']) this.actions['delete'].enable();
                    this.fireEvent('loadform', this.getForm(), result.data);
//                     if (this.getForm()) {
                        this.getForm().setValues(result.data);
                        this.getForm().resetDirty();
//todo: werte zwischenspeichern und setzen wenn form gerendered wurde?
//erstmal auskommentiert, da das eher nach hack aussschaut und womöglich eh gar nicht gebraucht wird...
//                     } else {
//                         this.on('renderform', function(form) {
//                             form.setValues(this.data);
//                             form.resetDirty();
//                         }, result);
//                     }
                }
                if (this.getForm()) {
                    this.getForm().clearInvalid();
                }
            },
            callback: function() {
                if (this.el) this.el.unmask();
            },
            scope: this
        });
    },

    //für AbstractPanel
    reset : function() {
        this.getForm().reset();
    },

    //deprecated
    onSubmit : function(options, successCallback) {
        if (!options) options = {};
        options.success = successCallback.callback;
        this.submit(options, successCallback);
    },

    //private
    onSave : function() {
        this.submit();
    },

    //für AbstractPanel
    submit: function(options)
    {
        this.getAction('save').disable();
        if (!options) options = {};

        var cb = {
            success: options.success,
            failure: options.failure,
            callback: options.callback,
            scope: options.scope || this
        };

        this.getForm().waitMsgTarget = this.el;
        this.getForm().submit(Ext.apply(options, {
            url: this.controllerUrl+'/jsonSave',
            waitMsg: 'saving...',
            success: function() {
                this.onSubmitSuccess.apply(this, arguments);
                if (cb.success) {
                    cb.success.apply(cb.scope, arguments)
                }
            },
            failure: function() {
                this.onSubmitFailure.apply(this, arguments);
                if (cb.failure) {
                    cb.failure.apply(cb.scope, arguments)
                }
            },
            callback: function() {
                if (cb.callback) {
                    cb.callback.apply(cb.scope, arguments)
                }
            },
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
        if (this.getForm().loadAfterSave) {
            //bei file-upload neu laden
            this.reload();
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
                        url: this.controllerUrl+'/jsonDelete',
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
        if (!this.getForm()) this.load(); //meta-daten wurden noch nicht geladen

        this.mabySave({
            callback: function() {
                this.enable();
                if (this.deleteButton) this.deleteButton.disable();
                this.getAction('delete').disable();
                this.applyBaseParams({id: 0});
                if (this.getForm()) {
                    this.getForm().setDefaultValues();
                    this.getForm().clearInvalid();
                }
                this.fireEvent('addaction', this);
            },
            scope: this
        });
    },
    findField: function(id) {
        return this.getForm().findField(id);
    },
    getForm : function() {
        if (!this.getFormPanel()) return null;
        return this.getFormPanel().getForm();
    },
    getFormPanel : function() {
        return this.formPanel;
    },
    setBaseParams : function(baseParams) {
        if (this.getForm()) {
            this.getForm().baseParams = baseParams;
        }
    },
    applyBaseParams : function(baseParams) {
        if (this.getForm()) {
            Ext.apply(this.getForm().baseParams, baseParams);
        }
    },
    getBaseParams : function() {
        return this.getForm().baseParams;
    }
});

Ext.reg('autoform', Vps.Auto.FormPanel);
