Ext4.define('Kwf.Ext4.Controller.Bindable.Form', {
    extend: 'Kwf.Ext4.Controller.Bindable.Abstract',

    formController: null,
    updateOnChange: false,
    focusOnAddSelector: 'field',

    constructor: function()
    {
        this.callParent(arguments);

        if (!this.formController) Ext4.Error.raise('formController config is required');
        if (!this.formController instanceof Kwf.Ext4.Controller.Form) Ext4.Error.raise('formController config needs to be a Kwf.Ext4.Controller.Form');

        this.formController.form.getForm().trackResetOnLoad = true;

        if (this.updateOnChange) {
            Ext4.each(this.formController.form.query('field'), function(i) {
                i.on('change', function() {
                    this.formController.form.updateRecord();
                }, this);
            }, this);
        }
    },

    load: function(row)
    {
        if (this.formController.form.isDisabled()) {
            Ext4.Error.raise('Can\'t load into disabled form');
        }
        this.formController.load(row);
    },

    reset: function()
    {
        this.formController.form.getForm().reset(true);
    },

    isDirty: function()
    {
        if (this.updateOnChange) return false;
        return this.formController.form.getForm().isDirty();
    },

    isValid: function()
    {
        return this.formController.form.getForm().isValid();
    },

    save: function(syncQueue)
    {
        return this.formController.save(syncQueue);
    },

    getLoadedRecord: function()
    {
        return this.formController.getLoadedRecord();
    },

    enable: function()
    {
        this.formController.form.enable();
    },
    disable: function()
    {
        this.formController.form.getForm()._record = null;
        Ext4.each(this.formController.form.query('field'), function(i) {
            i.setValue(null);
            i.resetOriginalValue();
        }, this);
        this.formController.form.disable();
    },
    getPanel: function()
    {
        return this.form;
    },

    onAdd: function()
    {
        if (this.focusOnAddSelector) {
            this.formController.form.down(this.focusOnAddSelector).focus();
            return true;
        }
    }
});
