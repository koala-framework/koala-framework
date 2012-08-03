Kwf.FrontendForm.ErrorStyle.IconBubble = Ext.extend(Kwf.FrontendForm.ErrorStyle.Above, {
    showErrors: function(r) {

        for (var fieldName in r.errorFields) {
            var field = this.form.findField(fieldName);
            field.el.addClass('kwfFieldError');
            if (!field.errorEl) {
                field.errorEl = field.el.createChild({
                    cls: 'kwfFieldErrorIconBubble'
                });
                field.errorEl.createChild({
                    cls: 'message'
                });
                field.errorEl.createChild({
                    cls: 'arrow'
                });
                field.errorEl.child('.message').enableDisplayMode('block');
                field.errorEl.child('.arrow').enableDisplayMode('block');
                field.errorEl.child('.message').hide();
                field.errorEl.child('.arrow').hide();
                if (field instanceof Kwf.FrontendForm.TextArea) {
                    field.errorEl.alignTo(field.el.child('textarea'), 'tr', [-20, 2]);
                } else if (field instanceof Kwf.FrontendForm.Radio) {
                    field.errorEl.alignTo(field.el.child('.kwfFormFieldRadio span:last'), 'tr', [0, 0]);
                } else if (field instanceof Kwf.FrontendForm.MultiCheckbox) {
                    field.errorEl.alignTo(field.el.child('input'), 'tr', [-6, -8]);
                } else if (field instanceof Kwf.FrontendForm.Checkbox) {
                    field.errorEl.alignTo(field.el.child('input'), 'tr', [-6, -8]);
                } else if (field.el.child('input')) {
                    field.errorEl.alignTo(field.el.child('input'), 'tr', [-20, 2]);
                } else if (field.el.child('select')) {
                    field.errorEl.alignTo(field.el.child('select'), 'tr', [-40, 2]);
                } else {
                    field.errorEl.alignTo(field.el, 'r', [-40, 2]);
                }
                field.errorEl.enableDisplayMode('block');
                field.errorEl.hide();
                Kwf.Event.on(field.errorEl, 'mouseEnter', function() {
                    this.errorEl.child('.message').fadeIn({duration: 0.4});
                    this.errorEl.child('.arrow').fadeIn({duration: 0.4});
                }, field);
                Kwf.Event.on(field.errorEl, 'mouseLeave', function() {
                    this.errorEl.child('.message').fadeOut({duration: 0.2});
                    this.errorEl.child('.arrow').fadeOut({duration: 0.2});
                }, field);
            }
            field.errorEl.child('.message').update(r.errorFields[fieldName]);
            field.errorEl.clearOpacity();
            field.errorEl.fadeIn({
                endOpacity: 1 //TODO read from css (but that's hard for IE)
            });
        }

        if (r.errorMessages && r.errorMessages.length) {
            this._showErrorMessagesAbove(r.errorMessages, r);
        }
    },
    hideFieldError: function(field)
    {
        field.el.removeClass('kwfFieldError');
        if (field.errorEl) field.errorEl.fadeOut();
    }
});
Kwf.FrontendForm.errorStyles['iconBubble'] = Kwf.FrontendForm.ErrorStyle.IconBubble;
