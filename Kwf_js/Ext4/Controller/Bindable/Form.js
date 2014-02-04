Ext4.define('Kwf.Ext4.Controller.Bindable.Form', {
    extend: 'Kwf.Ext4.Controller.Bindable.Abstract',

    form: null,
    updateOnChange: false,
    focusOnAddSelector: 'field',

    constructor: function()
    {
        this.callParent(arguments);

        this.form.getForm().trackResetOnLoad = true;

        if (this.updateOnChange) {
            Ext4.each(this.form.query('field'), function(i) {
                i.on('change', function() {
                    this.form.updateRecord();
                }, this);
            }, this);
        }
    },

    load: function(row)
    {
        this.form.loadRecord(row);
    },

    reset: function()
    {
        this.form.getForm().reset(true);
    },

    isDirty: function()
    {
        if (this.updateOnChange) return false;
        return this.form.getForm().isDirty()
    },

    isValid: function()
    {
        return this.form.getForm().isValid()
    },

    save: function()
    {
        if (!this.form.getRecord()) return;

        this.form.updateRecord();

        //trackResetOnLoad only calls resetOriginalValue on load, not on updateRecord
        Ext4.each(this.form.getRecord().fields.items, function(field) {
            var f = this.form.getForm().findField(field.name);
            if (f) {
                f.resetOriginalValue();
            }
        }, this);
    },

    getLoadedRecord: function()
    {
        return this.form.getRecord();
    },

    enable: function()
    {
        this.form.enable();
    },
    disable: function()
    {
        this.form.disable();
    },
    getPanel: function()
    {
        return this.form;
    },

    onAdd: function()
    {
        if (this.focusOnAddSelector) {
            this.form.down(this.focusOnAddSelector).focus();
        }
    }
});
