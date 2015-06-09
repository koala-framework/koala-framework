Kwf.FrontendForm.Radio = Ext2.extend(Kwf.FrontendForm.Field, {
    initField: function() {
        this.el.select('input').each(function(input) {
            input.on('click', function() {
                this.fireEvent('change', this.getValue());
            }, this);
        }, this);
    },
    getValue: function() {
        var ret = null;
        this.el.select('input').each(function(input) {
            if (input.dom.checked) {
                ret = input.dom.value;
            }
        }, this);
        return ret;
    },
    clearValue: function() {
        this.el.select('input').each(function(input) {
            input.dom.checked = false;
        }, this);
    },
    setValue: function(value) {
        this.el.select('input').each(function(input) {
            if (input.dom.value == value) {
                input.dom.checked = true;
            } else {
                input.dom.checked = false;
            }
        }, this);
    }
});

Kwf.FrontendForm.fields['kwfFormFieldRadio'] = Kwf.FrontendForm.Radio;
