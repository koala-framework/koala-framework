Vps.AutoForm.Dialog = function(renderTo, config)
{
    this.dialog = new Ext.BasicDialog(renderTo, {
        height: 420,
        width: 450,
        minHeight: 100,
        minWidth: 150,
        modal: true,
        proxyDrag: true,
        shadow: true
    });

    Vps.AutoForm.Dialog.superclass.constructor.call(this, this.dialog.body, config);


};
Ext.extend(Vps.AutoForm.Dialog, Vps.AutoForm.Form,
{
    renderButtons: function()
    {
        if (this.meta.formButtons.save) {
            this.saveButton = this.dialog.addButton({
                text    : 'Speichern',
                handler : this.onSubmit,
                scope   : this
            });
        }
    
        if (this.meta.formButtons.delete) {
            this.deleteButton = this.dialog.addButton({
                text    : 'Löschen',
                handler : this.onDelete,
                scope   : this
            });
        }

        if (this.meta.formButtons.add) {
            this.addButton = this.dialog.addButton({
                text    : 'Neuer Eintrag',
                handler : this.onMabyAdd,
                scope   : this
            });
        }

        if (this.meta.formButtons.cancel) {
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
        if(options.baseParams) this.form.baseParams = options.baseParams
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
        Vps.AutoForm.Dialog.superclass.onSubmitSuccess.call(this, form, action);
        this.hide();
    }
});