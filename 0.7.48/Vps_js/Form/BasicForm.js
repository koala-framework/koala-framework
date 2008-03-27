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
    }
});
