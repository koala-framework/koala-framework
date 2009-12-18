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
            if (field.rendered) field.clearValue();
        }, this);
    },

    //override stupid Ext behavior
    //better to ask the individual form fields
    //needed for: Checkbox, ComboBox, SwfUpload, Date...
    getValues: function() {
        var ret = {};
        this.items.each(function(field) {
            if (field.getName && field.getName()) {
                ret[field.getName()] = field.getValue();
            }
        }, this);
        return ret;
    }
});

//E-Mail Validierung darf ab Ext 2.2 keine Bindestriche mehr haben, jetzt schon wieder
Ext.apply(Ext.form.VTypes, {
	email:  function(v) {
        return /^([\w]+)(.[\w]*)*@([\w-]+\.){1,5}([A-Za-z]){2,4}$/.test(v);
    }
});
