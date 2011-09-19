Vps.FrontendForm.Radio = Ext.extend(Vps.FrontendForm.Field, {
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
});

Vps.FrontendForm.fields['vpsFormFieldRadio'] = Vps.FrontendForm.Radio;
