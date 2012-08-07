Kwf.FrontendForm.ErrorStyle.Above = Ext.extend(Kwf.FrontendForm.ErrorStyle.Abstract, {
    showErrors: function(r) {
        var errorMessages = r.errorMessages;
        for (var fieldName in r.errorFields) {
            errorMessages.push(r.errorFields[fieldName]);
        }
        if (errorMessages && errorMessages.length) {
            this._showErrorMessagesAbove(errorMessages, r);
        }
    },

    hideErrors: function()
    {
        Kwf.FrontendForm.ErrorStyle.Above.superclass.hideErrors.call(this);
        var error = this.form.el.parent().child('.webFormError');
        if (error) error.remove();
    }
});
Kwf.FrontendForm.errorStyles['above'] = Kwf.FrontendForm.ErrorStyle.Above;
