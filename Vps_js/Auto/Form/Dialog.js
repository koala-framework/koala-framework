/*
Vps.Auto.Form.Dialog = function(renderTo, config)
{
    if(!Ext.get(renderTo)) {
        renderTo = Ext.get(document.body).createChild();
    }
    config = config || {};
    this.dialog = new Ext.BasicDialog(renderTo, Ext.applyIf(config, {
        height: 420,
        width: 450,
        minHeight: 100,
        minWidth: 150,
        modal: true,
        proxyDrag: true,
        shadow: true,
        stateId: 'dialog-'+config.controllerUrl.replace(/\//g, '-').replace(/^-|-$/g, '') //um eine eindeutige id für den stateManager zu haben
    }));

    Vps.Auto.Form.Dialog.superclass.constructor.call(this, this.dialog.body, config);

    this.dialog.restoreState();

    if (config.showOnCreate) {
        this.show();
    }
};

Ext.extend(Vps.Auto.Form.Dialog, Vps.Auto.Form,
{
    renderButtons: function()
    {
        if (this.meta.buttons.save) {
            this.saveButton = this.dialog.addButton({
                text    : 'Speichern',
                handler : function() {
                    this.onSubmit();
                },
                scope   : this
            });
        }

        if (this.meta.buttons['delete']) {
            this.deleteButton = this.dialog.addButton({
                text    : 'Löschen',
                handler : function() {
                    this.onDelete();
                },
                scope   : this
            });
        }

        if (this.meta.buttons.add) {
            this.addButton = this.dialog.addButton({
                text    : 'Neuer Eintrag',
                handler : function() {
                    this.onMabyAdd();
                },
                scope   : this
            });
        }

        if (this.meta.buttons.cancel) {
            this.cancelButton = this.dialog.addButton({
                text    : 'Abbrechen',
                handler : function() {
                    this.hide();
                },
                scope   : this
            });
        }
    },
    show: function() {
        this.dialog.show();
    },
    hide: function() {
        this.dialog.hide();
    },
    add: function(options) {
        if(!options) options = {};
        if(options.baseParams) this.form.baseParams = options.baseParams
        this.form.baseParams.id = 0;
        this.onAdd();
        this.dialog.setTitle('hinzufügen');
        this.show();
    },
    edit: function(id, options) {
        this.load(id, options);
        this.dialog.setTitle('bearbeiten');
        this.show();
    },
    onSubmitSuccess: function(form, action) {
        Vps.Auto.Form.Dialog.superclass.onSubmitSuccess.call(this, form, action);
        this.hide();
    },
    clearInvalid: function() {
        this.form.clearInvalid();
    }
});*/