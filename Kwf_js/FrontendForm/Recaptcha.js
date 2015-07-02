Kwf.FrontendForm.Recaptcha = Kwf.extend(Kwf.FrontendForm.Field, {
    getFieldName: function() {
        return this.el.find('div[data-fieldname]').get(0).getAttribute('data-fieldname');
    },
    onError: function(message) {
        Recaptcha.reload(); //there can be only a single Recaptcha on the page
    }
});

Kwf.FrontendForm.fields['kwfFormFieldRecaptcha'] = Kwf.FrontendForm.Recaptcha;
