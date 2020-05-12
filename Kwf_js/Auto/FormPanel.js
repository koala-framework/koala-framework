Kwf.Auto.FormPanel = Ext2.extend(Kwf.Binding.AbstractPanel, {
    autoScroll: true, //um scrollbars zu bekommen
    border: false,
    maskDisabled: true,
    layout: 'fit',
    timeout: 30000,

    initComponent: function()
    {
        if (!this.formConfig) this.formConfig = {};
        if (!this.baseParams) this.baseParams = {};
        if (this.autoLoad !== false) {
            this.autoLoad = true;
        } else {
            delete this.autoLoad;
        }

        this.addEvents(
            'beforeloadform',
            'loadform',
            'deleteaction',
            'addaction',
            'renderform'
        );
        this.actions.save = new Ext2.Action({
            text    : trlKwf('Save'),
            icon    : KWF_BASE_URL+'/assets/silkicons/table_save.png',
            cls     : 'x2-btn-text-icon',
            handler : this.onSave,
            scope   : this
        });
        this.actions.saveBack = new Ext2.Action({
            text    : trlKwf('Save and Back'),
            icon    : KWF_BASE_URL+'/assets/silkicons/table_save.png',
            cls     : 'x2-btn-text-icon',
            handler : this.onSaveBack,
            scope   : this,
            hidden  : true //standardmäßig versteckt, ComponentPanel ruft show() auf
        });
        this.actions['delete'] = new Ext2.Action({
            text    : trlKwf('Delete'),
            icon    : KWF_BASE_URL+'/assets/silkicons/table_delete.png',
            cls     : 'x2-btn-text-icon',
            handler : this.onDelete,
            scope   : this
        });
        this.actions.add = new Ext2.Action({
            text    : trlKwf('New Entry'),
            icon    : KWF_BASE_URL+'/assets/silkicons/table_add.png',
            cls     : 'x2-btn-text-icon',
            handler : this.onAdd,
            scope   : this
        });

        Kwf.Auto.FormPanel.superclass.initComponent.call(this);

        if (!this.formConfig) this.formConfig = {};
        Ext2.applyIf(this.formConfig, {
            baseParams       : {},
            trackResetOnLoad : true,
            maskDisabled     : false
        });

        if (!this.controllerUrl) {
            throw new Error('No controllerUrl specified for AutoForm.');
        }
        this.formConfig.url = this.controllerUrl + '/json-save';
    },

    doAutoLoad : function()
    {
        //autoLoad kann in der zwischenzeit abgeschaltet werden, zB wenn
        //wir in einem Binding sind
        if (!this.autoLoad) return;

        this.load({}, {focusAfterLoad: this.focusAfterAutoLoad});
    },

    onMetaChange : function(meta)
    {
        Ext2.applyIf(meta.form, this.formConfig);

        if (this.baseCls) meta.form.baseCls = this.baseCls; //use the same

        for (var i in this.actions) {
            if (!meta.permissions[i] && this.getAction(i).hide) {
                this.getAction(i).hide();
            }
        }

        if (meta.form.loadAfterSave) {
            this.loadAfterSave = true;
        }

        if (meta.buttons && typeof meta.form.tbar == 'undefined') {
            for (var b in meta.buttons) {
                if (!meta.form.tbar) meta.form.tbar = [];
                meta.form.tbar.push(this.getAction(b));
            }
            if (meta.helpText) {
                meta.form.tbar.push('->');
                meta.form.tbar.push(new Ext2.Action({
                    icon : KWF_BASE_URL+'/assets/silkicons/information.png',
                    cls : 'x2-btn-icon',
                    handler : function (a) {
                        var helpWindow = new Ext2.Window({
                            html: meta.helpText,
                            width: 400,
                            bodyStyle: 'padding: 10px; background-color: white;',
                            autoHeight: true,
                            bodyBorder : false,
                            title: trlKwf('Info'),
                            resize: false
                        });
                        helpWindow.show();
                    },
                    scope: this
                }));
            }
        }
        if (this.formPanel != undefined) {
            this.remove(this.formPanel, true);
        }

        this.formPanel = new Ext2.FormPanel({
            baseCls: 'x2-plain',
            autoScroll: true,
            items: meta.form
        });
        this.formPanel.on('render', function() {
            this.fireEvent('renderform', this.getForm());
            this.fireEvent('loaded', this.getForm());
        }, this);
        this.add(this.formPanel);
        this.doLayout();
        this.setBaseParams(this.baseParams);
    },

    isDirty : function() {
        var f = this.getForm();
        if (!f) return false;
        return f.isDirty();
    },

    load : function(params, options) {

        if (this.el) this.el.mask(trlKwf('Loading...'));

        if (!params) params = {};

        //es kann auch direkt die id übergeben werden
        if (typeof params != 'object') params = { id: params };

        this.applyBaseParams(params);

        if (!this.getForm()) {
            params.meta = true; //wenn noch keine form vorhanden metaDaten anfordern
        }
        Ext2.applyIf(params, this.baseParams);

        if (this.getForm()) {
            this.getForm().clearValues();
            this.getForm().clearInvalid();
            this.getForm().resetDirty();
        }

        if (!this.loadConn) this.loadConn = new Kwf.Connection({ autoAbort: true });
        this.loadConn.request({
            mask: !this.el, //globale mask wenn kein el vorhanden
            loadOptions: options,
            url: this.controllerUrl+'/json-load',
            timeout: this.timeout,
            params: params,
            success: function(response, options, result) {
                if (result.meta) {
                    this.onMetaChange(result.meta);
                }
                if (result.data && this.getForm()) {
                    if (this.actions['delete']) this.actions['delete'].enable();
//                     if (this.getForm()) {
                        this.fireEvent('beforeloadform', this.getForm(), result.data);
                        this.getForm().setValues(result.data);
                        this.fireEvent('loadform', this.getForm(), result.data);
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
                    this.getForm().resetDirty();
                }
                var lo = options.loadOptions;
                if (lo && lo.focusAfterLoad) {
                    this.focusFirstField()
                }
                if (lo && lo.success) {
                    lo.success.call(lo.scope || this, this, result);
                }
            },
            failure: function(response, options, result){
                var lo = options.loadOptions;
                if (lo && lo.failure) {
                    lo.failure.call(lo.scope || this, this, result);
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
        var form = this.getForm();
        if (form) {
            form.reset();
            form.resetDirty();
        }
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

    onSaveBack : function() {
        this.submit({
            success: function() {
                this.fireEvent('savebackaction', this);
            },
            scope: this
        });
    },

    //für AbstractPanel
    submit: function(options)
    {
        if (!this.getForm().isValid()) {
            Ext2.Msg.alert(trlKwf('Save'),
                trlKwf("Can't save, please fill all red underlined fields correctly."));
            return;
        }

        this.getAction('save').disable();


        if (!options) options = {};

        var cb = {
            success: options.success,
            failure: options.failure,
            callback: options.callback,
            scope: options.scope || this
        };

        if ((!options.hideMaskText || options.hideMaskText != true) && this.el) this.el.mask(trlKwf('Saving...'));

        var params = this.getForm().getValues();
        params = Ext2.apply(params, this.getBaseParams());
        for (var i in params) {
            if (typeof params[i] == 'object') {
                params[i] = Ext2.encode(params[i]);
            } else if (typeof params[i] == 'boolean') {
                params[i] = params[i] ? '1' : '0';
            }
        }

        if (!params['id']) {
            params['avoid_reinsert_id'] = Math.random();
        }
        Ext2.Ajax.request(Ext2.apply(options, {
            params: params,
            timeout: this.timeout,
            url: this.controllerUrl+'/json-save',
            success: function() {
                this.onSubmitSuccess.apply(this, arguments);
                if (cb.success) {
                    cb.success.apply(cb.scope, arguments);
                }
            },
            failure: function() {
                this.onSubmitFailure.apply(this, arguments);
                if (cb.failure) {
                    cb.failure.apply(cb.scope, arguments);
                }
            },
            callback: function() {
                if (this.el) this.el.unmask();
                if (cb.callback) {
                    cb.callback.apply(cb.scope, arguments);
                }
            },
            scope: this
        }));
    },
    onSubmitFailure: function(form, action) {
        if(action.failureType == Ext2.form.Action.CLIENT_INVALID) {
            Ext2.Msg.alert(trlKwf('Save'),
                trlKwf("Can't save, please fill all red underlined fields correctly."));
        }
        this.getAction('save').enable();
    },

    onSubmitSuccess: function(response, options, result) {
        this.getForm().resetDirty();
        this.fireEvent('datachange', result);

        var reEnableSubmitButton = function() {
            this.getAction('save').enable();
        };
        reEnableSubmitButton.defer(1000, this);

        if(result.data && result.data.addedId) {
            this.getForm().baseParams.id = result.data.addedId;
            this.getAction('delete').enable();
            this.getAction('save').enable();
        }
        if (this.loadAfterSave) {
            this.reload();
        }
    },
    onDelete : function() {
        Ext2.Msg.show({
        title:trlKwf('delete?'),
        msg: trlKwf('Do you really want to delete this entry?'),
        buttons: Ext2.Msg.YESNO,
        scope: this,
        fn: function(button) {
            if (button == 'yes') {

                Ext2.Ajax.request({
                        url: this.controllerUrl+'/json-delete',
                        params: {id: this.getForm().baseParams.id},
                        success: function(response, options, r) {
                            this.disable();
                            this.getForm().clearValues();
                            this.getForm().clearInvalid();
                            this.getForm().resetDirty();
                            this.fireEvent('datachange', r);
                            this.fireEvent('deleteaction', this);
                        },
                        scope: this
                    });
            }
        }
        });
    },
    onAdd : function(options) {
        var cb = function() {
            if (this.deleteButton) this.deleteButton.disable();
            this.getAction('delete').disable();
            this.applyBaseParams({id: 0});
            this.getForm().setDefaultValues();
            this.getForm().clearInvalid();
            this.focusFirstField();
            this.fireEvent('addaction', this);

            if (this.ownerCt instanceof Ext2.TabPanel) {
                //wenn  form in einem tab, die form anzeigen
                //nach addaction, damit in grid an dem die form gebunden ist die activeId
                //auf 0 gesetzt werden kann
                this.ownerCt.setActiveTab(this);
            } else if (this.getFormPanel() && this.getFormPanel().items.first()
                        && this.getFormPanel().items.first().items.first()
                        && this.getFormPanel().items.first().items.first() instanceof Ext2.TabPanel
                        && this.getFormPanel().items.first().items.first().items.first()) {
                //und das gleiche auch noch wenn IN der form tabs sind
                //da den ersten tab öffnen
                var tabs = this.getFormPanel().items.first().items.first();
                tabs.setActiveTab(tabs.items.first());
            }

            if (options && options.success) {
                options.success.call(options.scope || this);
            }
        };
        if (!this.getForm()) {
            //meta-daten wurden noch nicht geladen
            this.load({}, {success: cb, scope: this});
        } else {
            if (this.mabySubmit({ callback: cb, scope: this})) {
                cb.call(this);
            }
        }
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
    _setFieldBaseParams: function() {
        if (this.getForm()) {
            this.cascade(function(item) {
                if (item.setFormBaseParams) {
                    item.setFormBaseParams(this.getForm().baseParams);
                }
            }, this);
        }
    },
    setBaseParams : function(baseParams) {
        this.baseParams = Kwf.clone(baseParams);
        if (this.getForm()) {
            this.getForm().baseParams = this.baseParams;
        }
        this._setFieldBaseParams();
    },
    applyBaseParams : function(baseParams) {
        Ext2.apply(this.baseParams, baseParams);
        if (this.getForm()) {
            Ext2.apply(this.getForm().baseParams, baseParams);
        }
        this._setFieldBaseParams();
    },
    getBaseParams : function() {
        return this.baseParams;
    },

    focusFirstField : function() {
        var form = this.getForm();
        if (!form) return;
        form.items.each(function(i) {
            if (i.isFormField && !i.disabled) {
                i.focus();
                return false;
            }
        }, this);
    },

    getSupportsAdd : function() {
        return true;
    }
});

Ext2.reg('kwf.autoform', Kwf.Auto.FormPanel);
