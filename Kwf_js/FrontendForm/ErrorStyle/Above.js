Kwf.FrontendForm.ErrorStyle.Above = Ext.extend(Kwf.FrontendForm.ErrorStyle.Abstract, {
    showErrors: function(r) {
        if (r.errorMessages && r.errorMessages.length) {
            this._showErrorMessagesAbove(r.errorMessages);
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
