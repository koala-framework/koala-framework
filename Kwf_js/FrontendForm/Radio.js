Kwf.FrontendForm.Radio = Ext.extend(Kwf.FrontendForm.Field, {
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
    }
});

Kwf.FrontendForm.fields['kwfFormFieldRadio'] = Kwf.FrontendForm.Radio;
