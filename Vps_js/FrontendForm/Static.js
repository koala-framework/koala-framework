Vps.FrontendForm.Static = Ext.extend(Vps.FrontendForm.Field, {
    initField: function() {
    },
    getValue: function() {
        return null;
    },
    getFieldName: function() {
        return null; //TODO?
    }
});
Vps.FrontendForm.fields['vpsFormFieldStatic'] = Vps.FrontendForm.Static;
