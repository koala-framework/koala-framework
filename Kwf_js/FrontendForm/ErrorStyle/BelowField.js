Kwf.FrontendForm.ErrorStyle.BelowField = Ext2.extend(Kwf.FrontendForm.ErrorStyle.Above, {
    showErrors: function(r) {

        for (var fieldName in r.errorFields) {
            var field = this.form.findField(fieldName);
            field.el.addClass('kwfFieldError');
            if (!field.errorEl) {
                if (field.el.up('.kwfFormContainerColumn')) {
                    field.errorEl = field.el.up('.kwfFormContainerColumn').up('.kwfFormContainerColumns').createChild({
                        cls: 'kwfFieldErrorMessage'
                    });
                } else {
                    field.errorEl = field.el.createChild({
                        cls: 'kwfFieldErrorMessage'
                    });
                }
                field.errorEl.enableDisplayMode('block');
            }
            field.errorEl.show();
            field.errorEl.update(r.errorFields[fieldName]);

        }
        if (r.errorMessages && r.errorMessages.length) {
            this._showErrorMessagesAbove(r.errorMessages, r);
        }
    },
    hideFieldError: function(field)
    {
        field.el.removeClass('kwfFieldError');
        if (field.errorEl) field.errorEl.hide();
    }
});
Kwf.FrontendForm.errorStyles['belowField'] = Kwf.FrontendForm.ErrorStyle.BelowField;
