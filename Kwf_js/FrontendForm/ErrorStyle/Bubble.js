Kwf.FrontendForm.ErrorStyle.Bubble = Ext.extend(Kwf.FrontendForm.ErrorStyle.Above, {
    showErrors: function(r) {

        for (var fieldName in r.errorFields) {
            var field = this.form.findField(fieldName);
            field.el.addClass('kwfFieldError');
            if (!field.errorEl) {
                field.errorEl = field.el.createChild({
                    cls: 'kwfFieldErrorBubble'
                });
                field.errorEl.createChild({
                    cls: 'message'
                });
                var closeButton = field.errorEl.createChild({
                    tag: 'a',
                    cls: 'closeButton'
                });
                closeButton.on('click', function(ev) {
                    ev.stopEvent();
                    this.fadeOut();
                }, field.errorEl);

                field.errorEl.alignTo(field.el, 'tr');
                field.errorEl.enableDisplayMode('block');
                field.errorEl.hide();
            }
            field.errorEl.child('.message').update(r.errorFields[fieldName]);
            field.errorEl.fadeIn();
        }

        if (r.errorMessages && r.errorMessages.length) {
            this._showErrorMessagesAbove(r.errorMessages);
        }
    },
    hideErrors: function()
    {
        Kwf.FrontendForm.ErrorStyle.Bubble.superclass.hideErrors.call(this);

        this.form.fields.each(function(field) {
            field.el.removeClass('kwfFieldError');
            if (field.errorEl) field.errorEl.fadeOut();
        }, this);
    }
});
Kwf.FrontendForm.errorStyles['bubble'] = Kwf.FrontendForm.ErrorStyle.Bubble;
