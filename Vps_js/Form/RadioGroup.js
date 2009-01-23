Vps.Form.RadioGroup = Ext.extend(Ext.form.RadioGroup, {
    initComponent: function() {
        Vps.Form.RadioGroup.superclass.initComponent.call(this);
    },
    afterRender: function() {
        Vps.Form.RadioGroup.superclass.afterRender.call(this);
        if (this.value) {
            this.setValue(this.value);
        }
    },
    getValue: function() {
        if (!this.rendered) {
            return this.value;
        } else {
            var ret = null;
            this.items.each(function(c) {
                if (c.getValue()) {
                    ret = c.inputValue;
                    return false;
                }
            }, this);
            return ret;
        }
    },
    setValue: function(v) {
        if (!this.rendered) {
            this.value = v;
        } else {
            this.items.each(function(c) {
                if (c.inputValue == v) {
                    c.setValue(true);
                } else {
                    c.setValue(false);
                }
            }, this);
        }
    }
});
Ext.reg('radiogroup', Vps.Form.RadioGroup);
