Kwf.FrontendForm.Recaptcha = Ext.extend(Kwf.FrontendForm.Field, {
    getFieldName: function() {
        return this.el.child('div[data-fieldname]').dom.getAttribute('data-fieldname');
    }
});

Kwf.FrontendForm.fields['kwfFormFieldRecaptcha'] = Kwf.FrontendForm.Recaptcha;
