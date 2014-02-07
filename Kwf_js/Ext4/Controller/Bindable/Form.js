Ext4.define('Kwf.Ext4.Controller.Bindable.Form', {
    extend: 'Kwf.Ext4.Controller.Bindable.Abstract',

    formController: null,
    updateOnChange: false,
    focusOnAddSelector: 'field',

    constructor: function()
    {
        this.callParent(arguments);

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
        return this.formController.form.getRecord();
    },

    enable: function()
    {
        this.formController.form.enable();
    },
    disable: function()
    {
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
