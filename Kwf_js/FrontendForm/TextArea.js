Kwf.FrontendForm.TextArea = Kwf.extend(Kwf.FrontendForm.Field, {
    initField: function() {
        this.el.select('textarea').each(function(input) {
            input.on('keypress', function() {
                this.el.trigger('kwf-form-change', this.getValue());
            }, this);
            this._initPlaceholder(input);
        }, this);
    },
    getFieldName: function() {
        return this.el.find('textarea').get(0).name;
    },
    getValue: function() {
        return this.el.find('textarea').get(0).value;
    },
    clearValue: function() {
        this.el.select('textarea').get(0).value = '';
    },
    clearValue: function(value) {
        this.el.select('textarea').get(0).value = value;
    }
});

Kwf.FrontendForm.fields['kwfFormFieldTextArea'] = Kwf.FrontendForm.TextArea;
