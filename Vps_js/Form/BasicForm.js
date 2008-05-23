Ext.form.BasicForm.override({
    resetDirty: function() {
        this.items.each(function(field) {
            field.resetDirty();
        });
    },
    setDefaultValues: function() {
        this.items.each(function(field) {
            field.setDefaultValue();
        }, this);
    },
    clearValues: function() {
        this.items.each(function(field) {
            field.clearValue();
        }, this);
    },

    //override stupid Ext behavior
    //better to ask the individual form fields
    //needed for: Checkbox, ComboBox, SwfUpload, Date...
    getValues: function() {
        var ret = {};
        this.items.each(function(field) {
            var v = field.getValue();
            if (v instanceof Date) {
                v = v.format("Y-m-d");
            }
            ret[field.getName()] = v;
        }, this);
        return ret;
    }
});
