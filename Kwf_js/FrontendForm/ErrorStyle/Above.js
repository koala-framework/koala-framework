Kwf.FrontendForm.ErrorStyle.Above = Ext2.extend(Kwf.FrontendForm.ErrorStyle.Abstract, {
    showErrors: function(r) {
        var errorMessages = r.errorMessages;
        for (var fieldName in r.errorFields) {
            errorMessages.push(r.errorFields[fieldName]);
            var field = this.form.findField(fieldName);
            field.el.addClass('kwfFieldError');
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
    },

    hideFieldError: function(field)
    {
        field.el.removeClass('kwfFieldError');
        if (field.errorEl) field.errorEl.hide();
    }
});
Kwf.FrontendForm.errorStyles['above'] = Kwf.FrontendForm.ErrorStyle.Above;
