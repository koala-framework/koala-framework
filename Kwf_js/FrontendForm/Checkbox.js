Kwf.FrontendForm.Checkbox = Ext.extend(Kwf.FrontendForm.Field, {
    initField: function() {
        this.el.select('input').each(function(input) {
            input.on('click', function() {
                this.fireEvent('change', this.getValue());
            }, this);
        }, this);
    }
});

Kwf.FrontendForm.fields['kwfFormFieldCheckbox'] = Kwf.FrontendForm.Checkbox;
