Vps.FrontendForm.TextArea = Ext.extend(Vps.FrontendForm.Field, {
    initField: function() {
        this.el.select('textarea').each(function(input) {
            input.on('keypress', function() {
                this.fireEvent('change', this.getValue());
            }, this);
        }, this);
    },
    getFieldName: function() {
        return this.el.child('textarea').dom.name;
    },
    getValue: function() {
        return this.el.child('textarea').dom.value;
    },
});

Vps.FrontendForm.fields['vpsFormFieldTextArea'] = Vps.FrontendForm.TextArea;
