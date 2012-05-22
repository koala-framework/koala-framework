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
                if (field instanceof Kwf.FrontendForm.TextArea) {
                    field.errorEl.alignTo(field.el.child('textarea'), 'bl');
                } else if (field.el.child('input')) {
                    field.errorEl.alignTo(field.el.child('input'), 'bl');
                } else {
                    field.errorEl.alignTo(field.el, 'bl');
                }
                field.errorEl.enableDisplayMode('block');
                field.errorEl.hide();
            }
            field.errorEl.child('.message').update(r.errorFields[fieldName]);
            field.errorEl.clearOpacity();
            field.errorEl.fadeIn({
                endOpacity: 0.8 //TODO read from css (but that's hard for IE)
            });
        }

        if (r.errorMessages && r.errorMessages.length) {
            this._showErrorMessagesAbove(r.errorMessages);
        }
    },
    hideFieldError: function(field)
    {
        field.el.removeClass('kwfFieldError');
        if (field.errorEl) field.errorEl.fadeOut();
    }
});
Kwf.FrontendForm.errorStyles['bubble'] = Kwf.FrontendForm.ErrorStyle.Bubble;
