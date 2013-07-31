Kwf.FrontendForm.ErrorStyle.IconBubble = Ext.extend(Kwf.FrontendForm.ErrorStyle.Above, {
    showErrors: function(r) {

        var firstField = null;
        for (var fieldName in r.errorFields) {
            var field = this.form.findField(fieldName);
            if (!firstField) { firstField = field; }
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
                    field.errorEl.alignTo(field.el.child('textarea'), 'tr', [-20-field.el.child('textarea').getBorderWidth("r"), 2+field.el.child('textarea').getBorderWidth("t")]);
                } else if (field instanceof Kwf.FrontendForm.Radio) {
                    field.errorEl.alignTo(field.el.child('.kwfFormFieldRadio span:last'), 'r-r', [0, 0]);
                } else if (field instanceof Kwf.FrontendForm.MultiCheckbox) {
                    field.errorEl.alignTo(field.el.child('input'), 'r-r', [10-field.el.child('input').getBorderWidth("r"), 0]);
                } else if (field instanceof Kwf.FrontendForm.Checkbox) {
                    field.errorEl.alignTo(field.el.child('input'), 'r-r', [-6-field.el.child('input').getBorderWidth("r"), 0]);
                } else if (field.el.child('input')) {
                    if (field.el.child('input').getWidth() < 40) {
                        field.errorEl.alignTo(field.el.child('input'), 'r-r', [10-field.el.child('input').getBorderWidth("r"), 0]);
                    } else {
                        field.errorEl.alignTo(field.el.child('input'), 'r-r', [-10-field.el.child('input').getBorderWidth("r"), 0]);
                    }
                } else if (field.el.child('select')) {
                    if (field.el.child('select').getWidth() < 60) {
                        field.errorEl.alignTo(field.el.child('select'), 'r-r', [10-field.el.child('select').getBorderWidth("r"), 0]);
                    } else {
                        field.errorEl.alignTo(field.el.child('select'), 'r-r', [-10-field.el.child('select').getBorderWidth("r"), 0]);
                    }
                } else {
                    field.errorEl.alignTo(field.el, 'r-r', [-10, 0]);
                }
                field.errorEl.enableDisplayMode('block');
                field.errorEl.hide();
                
                Kwf.Event.on(Ext.get(field.el), 'mouseEnter', function() {
                    if (firstField) {
                        firstField.errorEl.child('.message').stopFx().fadeOut({duration: 0.4});
                        firstField.errorEl.child('.arrow').stopFx().fadeOut({duration: 0.4});
                    }
                    this.errorEl.child('.message').stopFx().fadeIn({duration: 0.4});
                    this.errorEl.child('.arrow').stopFx().fadeIn({duration: 0.4});
                }, field);
                Kwf.Event.on(Ext.get(field.el), 'mouseLeave', function() {
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
        if (field.errorEl) field.errorEl.fadeOut();
    }
});
Kwf.FrontendForm.errorStyles['iconBubble'] = Kwf.FrontendForm.ErrorStyle.IconBubble;
