Kwf.FrontendForm.Select = Ext2.extend(Kwf.FrontendForm.Field, {
    initField: function() {
        this.el.select('select').each(function(input) {
            input.on('change', function() {
                this.fireEvent('change', this.getValue());
            }, this);
        }, this);
    },
    getFieldName: function() {
        return this.el.child('select').dom.name;
    },
    getValue: function() {
        return this.el.child('select').dom.value;
    },
    clearValue: function() {
        this.el.child('select').dom.value = '';
    },
    setValue: function(value) {
        this.el.child('select').dom.value = value;
    }
});

Kwf.FrontendForm.fields['kwfFormFieldSelect'] = Kwf.FrontendForm.Select;
