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

Ext.apply(Ext.form.VTypes, {
    //E-Mail Validierung darf ab Ext 2.2 keine Bindestriche mehr haben, jetzt schon wieder
	email:  function(v) {
        return /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/.test(v);
    },
    time: function(val, field) {
        return /^([0-9]{2}):([0-9]{2}):([0-9]{2})$/i.test(val);
    },
    timeText: trlVps('Not a valid time.  Must be in the format "12:34:00".'),
    timeMask: /[\d:]/i
});
