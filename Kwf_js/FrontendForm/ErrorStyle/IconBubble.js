Kwf.FrontendForm.ErrorStyle.IconBubble = Ext2.extend(Kwf.FrontendForm.ErrorStyle.Above, {
    showErrors: function(r) {

        var firstField = null;
        for (var fieldName in r.errorFields) {
            var field = this.form.findField(fieldName);
            if (!firstField) { firstField = field; }
            field.el.addClass('kwfFieldError');
            if (!field.errorEl) {
                field.errorEl = field.el.child('.kwfFormFieldWrapper').createChild({
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
                field.errorEl.enableDisplayMode('block');
                field.errorEl.hide();

                Kwf.Event.on(Ext2.get(field.el), 'mouseEnter', function() {
                    if (firstField) {
                        firstField.errorEl.child('.message').stopFx().fadeOut({duration: 0.4});
                        firstField.errorEl.child('.arrow').stopFx().fadeOut({duration: 0.4});
                    }
                    this.errorEl.child('.message').stopFx().fadeIn({duration: 0.4});
                    this.errorEl.child('.arrow').stopFx().fadeIn({duration: 0.4});
                }, field);
                Kwf.Event.on(Ext2.get(field.el), 'mouseLeave', function() {
                    this.errorEl.child('.message').stopFx().fadeOut({duration: 0.2});
                    this.errorEl.child('.arrow').stopFx().fadeOut({duration: 0.2});
                }, field);
            }
            field.errorEl.child('.message').update(r.errorFields[fieldName]);
            field.errorEl.clearOpacity();
            field.errorEl.fadeIn({
                endOpacity: 1 //TODO read from css (but that's hard for IE)
            });
        }
        if (firstField) {
            firstField.errorEl.child('.message').stopFx().fadeIn({duration: 0.4});
            firstField.errorEl.child('.arrow').stopFx().fadeIn({duration: 0.4});
            firstField.errorEl.child('.message').fadeOut.defer(4000, firstField.errorEl.child('.message'));
            firstField.errorEl.child('.arrow').fadeOut.defer(4000, firstField.errorEl.child('.arrow'));
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
