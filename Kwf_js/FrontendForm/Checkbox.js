Kwf.FrontendForm.Checkbox = Kwf.extend(Kwf.FrontendForm.Field, {
    initField: function() {
        this.el.select('input').each(function(input) {
            input.on('click', function() {
                this.el.trigger('kwf-form-change', this.getValue());
            }, this);
        }, this);
    },
    clearValue: function() {
        var inp = this.el.find('input');
        inp.get(0).checked = false;
    },
    setValue: function(value) {
        var inp = this.el.find('input');
        inp.get(0).checked = !!value;
    },
    getValue: function(value) {
        var inp = this.el.find('input');
        return inp.get(0).checked;
    }
});

Kwf.FrontendForm.fields['kwfFormFieldCheckbox'] = Kwf.FrontendForm.Checkbox;
