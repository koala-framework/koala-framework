Ext4.define('Kwf.Ext4.Controller.Form', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    constructor: function(config) {
        this.mixins.observable.constructor.call(this, config);
        this.init();
    },

    init: function()
    {
        var form = this.form;
        if (!this.saveButton) this.saveButton = form.down('button#save');
        if (this.saveButton) {
            this.saveButton.on('click', this.onSaveClick, this);
        }
    },

    load: function(row)
    {
        this.form.loadRecord(row);
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
    }
});
