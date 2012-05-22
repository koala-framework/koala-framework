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
                    field.errorEl.alignTo(field.el.child('textarea'), 'r', [-40, 4]);
                } else if (field instanceof Kwf.FrontendForm.Radio) {
                    field.errorEl.alignTo(field.el.child('.kwfFormFieldRadio span:last'), 'r', [-30, 6]);
                } else if (field.el.child('input')) {
                    field.errorEl.alignTo(field.el.child('input'), 'r', [-40, 4]);
                } else {
                    field.errorEl.alignTo(field.el, 'r', [-40, 4]);
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
