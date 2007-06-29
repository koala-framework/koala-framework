Ext.namespace('Vps.AutoForm');

Vps.AutoForm.Form = function(config)
{
    this.renderTo = config.renderTo;
    this.controllerUrl = config.controllerUrl;
    delete config.controllerUrl;
    delete config.renderTo;

    config = Ext.applyIf(config, {
        url: this.controllerUrl+'jsonSave',
        waitMsgTarget: document.body,
        trackResetOnLoad: true,
        baseParams: {}
    });
    this.form = new Ext.form.Form(config);
    
    this.addEvents({
        generatetoolbar: true,
        dataChanged: true,
        formRendered: true,
        deleted: true,
        add: true,
        loaded: true
    });

    this.form.doAction('loadAutoForm', {
        url: this.controllerUrl+'jsonLoad',
        meta: this.onMetaChange,
        scope: this
    });
};

Ext.extend(Vps.AutoForm.Form, Ext.util.Observable,
{
    checkDirty: false,

    renderButtons: function()
    {
        var layout = new Ext.BorderLayout(this.renderTo, {
                north: {split: false, initialSize: 30},
                center: { autoScroll: true }
            });
        layout.beginUpdate();

        var ToolbarContentPanel = new Ext.ContentPanel({autoCreate: true, fitToFrame:true, closable:false});
        layout.add('north', ToolbarContentPanel);

        var FormContentPanel = new Ext.ContentPanel({autoCreate: true, fitToFrame:true, closable:false});
        layout.add('center', FormContentPanel);
        this.renderTo = FormContentPanel.getEl();

        layout.endUpdate();

        this.toolbar = new Ext.Toolbar(ToolbarContentPanel.getEl());
        if (this.meta.formButtons.save) {
            this.saveButton = this.toolbar.addButton({
                text    : 'Speichern',
                icon    : '/assets/vps/images/silkicons/table_save.png',
                cls     : 'x-btn-text-icon',
                handler : function() {
                    this.onSubmit();
                },
                scope   : this
            });
        }
    
        if (this.meta.formButtons.delete) {
            this.deleteButton = this.toolbar.addButton({
                text    : 'Löschen',
                icon    : '/assets/vps/images/silkicons/table_delete.png',
                cls     : 'x-btn-text-icon',
                handler : function() {
                    this.onDelete();
                },
                scope   : this
            });
        }

        if (this.meta.formButtons.add) {
            this.toolbar.addSeparator();
            var c = {};
            if(typeof this.meta.formButtons.add == 'object') {
                c = this.meta.formButtons.add;
            }
            this.addButton = this.toolbar.addButton(Ext.applyIf(c, {
                text    : 'Neuer Eintrag',
                icon    : '/assets/vps/images/silkicons/table_add.png',
                cls     : 'x-btn-text-icon',
                handler : function() {
                    this.onAdd();
                },
                scope   : this
            }));
        }
        this.fireEvent('generatetoolbar', this.toolbar);
    },
    onMetaChange : function(meta)
    {
        this.meta = meta;
        if (meta.formButtons) {
            this.renderButtons();
        }

        var hasTabs = false;
        meta.formFields.each(function(field) {
            for (var i in field) {
                if (i == 'tab') {
                    hasTabs = true;
                }
            }
        });
        if (hasTabs) {
            var tabContainers = [];
            var frmContainerTabs = this.container();
            this.end();
        }

        meta.formFields.each(function(field) {
            if (typeof field == 'String') {
                try {
                    this.form.add(eval(field));
                } catch(e) {
                    throw "invalid field: "+field;
                }
            } else {
                var fieldType = field.type;
                delete field.type;
                if (!fieldType) {
                    //ignore field
                } else if (fieldType == 'column') {
                    this.form.column(field);
                } else if (fieldType == 'fieldset') {
                    this.form.fieldset(field);
                } else if (fieldType == 'end') {
                    this.form.end();
                } else if (fieldType == 'tab') {
                    tabContainers.push(this.container({
                        el: Ext.DomHelper.append(formRenderTo, {tag:'div', style:'padding:20px'})
                    }));
                } else if (Vps.Form[fieldType]) {
                    this.form.add(new Vps.Form[fieldType](field))
                } else if (Ext.form[fieldType]) {
                    this.form.add(new Ext.form[fieldType](field))
                } else {
                    try {
                        fieldType = eval(fieldType);
                        this.form.add(new fieldType(field))
                    } catch(e) {
                        throw "invalid field: "+fieldType;
                    }
                }
            }
        }, this);
        this.form.render(this.renderTo);
        if (hasTabs) {
            var tabPanel = new Ext.TabPanel(frmContainerTabs.el);
            tabContainers.each(function(tabContainer) {
                tabPanel.addTab(tabContainer.getEl(), 'Tab');
            });
            tabPanel.activate(0);
        }

        this.fireEvent("formRendered", this);
    },
    load : function(id, options) {
        if (!this.form.baseParams) this.form.baseParams = {};
        this.form.baseParams.id = id;
        if (!options) options = {};
        this.form.load(Ext.applyIf(options, {
            url: this.controllerUrl+'jsonLoad',
            waitMsg: 'laden...',
            success: function(form, action) {
                this.fireEvent("loaded", form, action);
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
        this.saveButton.disable();
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
                this.saveButton.enable();
            },
            scope: this
        }));
    },
    onSubmitSuccess: function(form, action) {
        this.form.resetDirty();
        this.fireEvent("dataChanged", action.result);

        var reEnableSubmitButton = function() {
            this.saveButton.enable();;
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
                        params: {id: this.baseParams.id},
                        success: function(response, options, r) {
                            this.fireEvent("dataChanged", r);
                            this.form.clearValues();
                            this.disable();
                            this.fireEvent("deleted", this);
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
                this.form.baseParams.id = 0;
                this.form.setDefaultValues();
                this.fireEvent("add", this);
            },
            scope: this
        });
    },
    findField: function(id) {
        return this.form.findField(id);
    },
    disable : function() {
        if(this.saveButton) this.saveButton.disable();
        if(this.deleteButton) this.deleteButton.disable();
        this.form.items.each(function(b) {
            b.disable();
        });
    },
    enable : function() {
        if (this.toolbar) {
            this.toolbar.items.each(function(b) {
                b.enable();
            });
        }
        this.form.items.each(function(b) {
            b.enable();
        });
    }
});
