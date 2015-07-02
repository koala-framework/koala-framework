Kwf.FrontendForm.ErrorStyle.IconBubble = Kwf.extend(Kwf.FrontendForm.ErrorStyle.Above, {
    showErrors: function(r) {

        var firstField = null;
        for (var fieldName in r.errorFields) {
            var field = this.form.findField(fieldName);
            if (!firstField) { firstField = field; }
            field.el.addClass('kwfFieldError');
            if (!field.errorEl) {
                field.errorEl = field.el.find('.kwfFormFieldWrapper').append('<div class="kwfFieldErrorIconBubble"></div>');
                field.errorEl.append('<div class="message"></div>');
                field.errorEl.append('<div class="arrow"></div>');
                field.errorEl.find('.message').hide();
                field.errorEl.find('.arrow').hide();
                field.errorEl.hide();

                field.el.on('mouseenter', (function() {
                    if (firstField) {
                        firstField.errorEl.find('.message').stopFx().fadeOut({duration: 0.4});
                        firstField.errorEl.find('.arrow').stopFx().fadeOut({duration: 0.4});
                    }
                    this.errorEl.find('.message').stopFx().fadeIn({duration: 0.4});
                    this.errorEl.find('.arrow').stopFx().fadeIn({duration: 0.4});
                }).bind(this));
                field.el.on('mouseleave', (function() {
                    this.errorEl.find('.message').stopFx().fadeOut({duration: 0.2});
                    this.errorEl.find('.arrow').stopFx().fadeOut({duration: 0.2});
                }).bind(this));
            }
            field.errorEl.find('.message').update(r.errorFields[fieldName]);
            field.errorEl.clearOpacity();
            field.errorEl.fadeIn({
                endOpacity: 1 //TODO read from css (but that's hard for IE)
            });
        }
        if (firstField) {
            firstField.errorEl.find('.message').stopFx().fadeIn({duration: 0.4});
            firstField.errorEl.find('.arrow').stopFx().fadeIn({duration: 0.4});
            firstField.errorEl.find('.message').fadeOut.defer(4000, firstField.errorEl.find('.message'));
            firstField.errorEl.find('.arrow').fadeOut.defer(4000, firstField.errorEl.find('.arrow'));
        }

        if (r.errorMessages && r.errorMessages.length) {
            this._showErrorMessagesAbove(r.errorMessages, r);
        }
    },
    hideFieldError: function(field)
    {
        field.el.removeClass('kwfFieldError');
        if (field.errorEl && field.errorEl.isVisible() && !field.errorEl.fadingOut) {
            field.errorEl.hide();
            Kwf.callOnContentReady(field.el, {newRender: false});
            field.errorEl.show();
            field.errorEl.fadeOut({
                callback: function() {
                    field.errorEl.fadingOut = false;
                }
            });
            field.errorEl.fadingOut = true;
        }
    }
});
Kwf.FrontendForm.errorStyles['iconBubble'] = Kwf.FrontendForm.ErrorStyle.IconBubble;
