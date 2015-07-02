Kwf.FrontendForm.Select = Kwf.extend(Kwf.FrontendForm.Field, {
    initField: function() {
        this.el.select('select').each(function(input) {
            input.on('change', function() {
                this.el.trigger('kwf-form-change', this.getValue());
            }, this);
        }, this);
    },
    getFieldName: function() {
        return this.el.find('select').get(0).name;
    },
    getValue: function() {
        return this.el.find('select').get(0).value;
    },
    clearValue: function() {
        this.el.find('select').get(0).value = '';
    },
    setValue: function(value) {
        this.el.find('select').get(0).value = value;
    }
});

Kwf.FrontendForm.fields['kwfFormFieldSelect'] = Kwf.FrontendForm.Select;
