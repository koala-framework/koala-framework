Kwf.FrontendForm.Static = Kwf.extend(Kwf.FrontendForm.Field, {
    initField: function() {
    },
    getValue: function() {
        return null;
    },
    getFieldName: function() {
        return null; //TODO?
    }
});
Kwf.FrontendForm.fields['kwfFormFieldStatic'] = Kwf.FrontendForm.Static;
