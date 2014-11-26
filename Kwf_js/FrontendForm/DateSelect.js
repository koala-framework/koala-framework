Kwf.FrontendForm.DateSelect = Ext2.extend(Kwf.FrontendForm.Field, {
    initField: function() {
        this.el.select('select').each(function(input) {
            input.on('change', function() {
                this.fireEvent('change', this.getValue());
            }, this);
        }, this);
    },
    getFieldName: function() {
        var name = this.el.child('select').dom.name;
        return name.substr(0, name.length-4);
    },
    getValue: function() {
        var value = {};
        this.el.select('select').each(function(input) {
            if (input.dom.name.substr(-4) == '_day') {
                value['day'] = input.dom.value;
            } else if (input.dom.name.substr(-6) == '_month') {
                value['month'] = input.dom.value;
            } else if (input.dom.name.substr(-5) == '_year') {
                value['year'] = input.dom.value;
            }
        }, this);
        return value['year'] + '-' + value['month'] + '-' + value['day'];
    },
    clearValue: function() {
        this.el.select('select').each(function(input) {
            input.dom.value='';
        }, this);
    },
    // TODO: not yet implemented
    setValue: function(value) {
    }
});

Kwf.FrontendForm.fields['kwfFormFieldDateSelect'] = Kwf.FrontendForm.DateSelect;
