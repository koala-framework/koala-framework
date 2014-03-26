Ext4.define('Kwf.Ext4.Controller.Form', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    autoLoadComboBoxStores: true,

    form: null,

    constructor: function(config) {
        this.mixins.observable.constructor.call(this, config);
        this.init();
    },

    init: function()
    {
        if (!this.form) Ext4.Error.raise('form config is required');
        if (!this.form instanceof Ext4.form.Panel) Ext4.Error.raise('form config needs to be a Ext.form.Panel');

        var form = this.form;
        if (typeof this.saveButton == 'undefined') this.saveButton = form.down('button#save');
        if (this.saveButton && !this.saveButton instanceof Ext4.button.Button) Ext4.Error.raise('saveButton config needs to be a Ext.button.Button');
        if (this.saveButton) {
            this.saveButton.on('click', this.onSaveClick, this);
        }
    },

    load: function(row)
    {
        if (this.autoLoadComboBoxStores) {
            this.form.query("combobox").each(function(i) {
                if (i.queryMode == 'remote' && i.store && !i.store.lastOptions) {
                    i.store.load();
                }
            }, this);
        }

        //when loading the same row (by comparing the id) keep dirty values
        var keepDirtyValues = this.form.getForm()._record
            && this.form.getForm()._record.getId() == row.getId();

        this.form.getForm()._record = row;

        // Suspend here because setting the value on a field could trigger
        // a layout, for example if an error gets set, or it's a display field
        Ext4.suspendLayouts();
        Ext4.iterate(row.getData(), function(fieldId, val) {
            var field = this.form.getForm().findField(fieldId);
            if (field) {
                if (keepDirtyValues && field.isDirty()) {
                    return;
                }
                field.setValue(val);
                field.resetOriginalValue();
            }
        }, this);
        Ext4.resumeLayouts(true);
    },

    onSaveClick: function()
    {
        if (!this.form.getForm().isValid()) {
            Ext4.Msg.alert(trlKwf('Save'),
                trlKwf("Can't save, please fill all red underlined fields correctly."));
            return false;
        }

        this.save();

        this.form.getRecord().save({
            success: function() {
                this.fireEvent('savesuccess', this.form.getRecord());
            },
            scope: this
        });
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
    }
});
