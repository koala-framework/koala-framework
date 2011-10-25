Kwf.FrontendForm.Select = Ext.extend(Kwf.FrontendForm.Field, {
    initField: function() {
        this.el.select('select').each(function(input) {
            input.on('click', function() {
                this.fireEvent('change', this.getValue());
            }, this);
        }, this);
    },
    getFieldName: function() {
        return this.el.child('select').dom.name;
    },
    getValue: function() {
        return this.el.child('select').dom.value;
    }
});

Kwf.FrontendForm.fields['kwfFormFieldSelect'] = Kwf.FrontendForm.Select;
