Vps.Form.RadioGroup = Ext.extend(Ext.form.RadioGroup, {
    initComponent: function() {
        Vps.Form.RadioGroup.superclass.initComponent.call(this);
    },
    getValue: function() {
        var ret = null;
        this.items.each(function(c) {
            if (c.getValue()) {
                ret = c.inputValue;
                return false;
            }
        }, this);
        return ret;
    },
    setValue: function(v) {
        this.items.each(function(c) {
            if (c.inputValue == v) {
                c.setValue(true);
            } else {
                c.setValue(false);
            }
        }, this);
    }
});
Ext.reg('radiogroup', Vps.Form.RadioGroup);
