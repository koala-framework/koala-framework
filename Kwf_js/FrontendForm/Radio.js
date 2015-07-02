Kwf.FrontendForm.Radio = Kwf.extend(Kwf.FrontendForm.Field, {
    initField: function() {
        this.el.select('input').each(function(input) {
            input.on('click', function() {
                this.el.trigger('kwf-form-change', this.getValue());
            }, this);
        }, this);
    },
    getValue: function() {
        var ret = null;
        this.el.select('input').each(function(input) {
            if (input.get(0).checked) {
                ret = input.get(0).value;
            }
        }, this);
        return ret;
    },
    clearValue: function() {
        this.el.select('input').each(function(input) {
            input.get(0).checked = false;
        }, this);
    },
    setValue: function(value) {
        this.el.select('input').each(function(input) {
            if (input.get(0).value == value) {
                input.get(0).checked = true;
            } else {
                input.get(0).checked = false;
            }
        }, this);
    }
});

Kwf.FrontendForm.fields['kwfFormFieldRadio'] = Kwf.FrontendForm.Radio;
