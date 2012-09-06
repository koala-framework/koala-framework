Kwf.FrontendForm.Checkbox = Ext.extend(Kwf.FrontendForm.Field, {
    initField: function() {
        this.el.select('input').each(function(input) {
            input.on('click', function() {
                this.fireEvent('change', this.getValue());
            }, this);
        }, this);
    },
    clearValue: function() {
        var inp = this.el.child('input');
        inp.dom.checked = false;
    }
});

Kwf.FrontendForm.fields['kwfFormFieldCheckbox'] = Kwf.FrontendForm.Checkbox;
