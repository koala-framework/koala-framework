Kwf.FrontendForm.TextArea = Ext.extend(Kwf.FrontendForm.Field, {
    initField: function() {
        this.el.select('textarea').each(function(input) {
            input.on('keypress', function() {
                this.fireEvent('change', this.getValue());
            }, this);
            this._initPlaceholder(input);
        }, this);
    },
    getFieldName: function() {
        return this.el.child('textarea').dom.name;
    },
    getValue: function() {
        return this.el.child('textarea').dom.value;
    },
    clearValue: function() {
        this.el.select('textarea').dom.value = '';
    },
    clearValue: function(value) {
        this.el.select('textarea').dom.value = value;
    }
});

Kwf.FrontendForm.fields['kwfFormFieldTextArea'] = Kwf.FrontendForm.TextArea;
