Kwf.FrontendForm.Recaptcha = Ext2.extend(Kwf.FrontendForm.Field, {
    getFieldName: function() {
        return this.el.child('div[data-fieldname]').dom.getAttribute('data-fieldname');
    },
    onError: function(message) {
        Recaptcha.reload(); //there can be only a single Recaptcha on the page
    }
});

Kwf.FrontendForm.fields['kwfFormFieldRecaptcha'] = Kwf.FrontendForm.Recaptcha;
