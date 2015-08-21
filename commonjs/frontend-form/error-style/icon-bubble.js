var ErrorStyleAbove = require('kwf/frontend-form/error-style/above');
var kwfExtend = require('kwf/extend');
var errorStyleRegistry = require('kwf/frontend-form/error-style-registry');
var onReady = require('kwf/on-ready');

var ErrorStyleIconBubble = kwfExtend(ErrorStyleAbove, {
    showErrors: function(r) {

        var firstField = null;
        for (var fieldName in r.errorFields) {
            var field = this.form.findField(fieldName);
            if (!firstField) { firstField = field; }
            field.el.addClass('kwfUp-kwfFieldError');
            if (!field.errorEl) {
                field.errorEl = field.el.find('.kwfUp-kwfFormFieldWrapper').append('<div class="kwfUp-kwfFieldErrorIconBubble"></div>');
                field.errorEl.append('<div class="kwfUp-message"></div>');
                field.errorEl.append('<div class="kwfUp-arrow"></div>');
                field.errorEl.find('.kwfUp-message').hide();
                field.errorEl.find('.kwfUp-arrow').hide();
                field.errorEl.hide();

                field.el.on('mouseenter', (function() {
                    if (firstField) {
                        firstField.errorEl.find('.kwfUp-message').stopFx().fadeOut({duration: 0.4});
                        firstField.errorEl.find('.kwfUp-arrow').stopFx().fadeOut({duration: 0.4});
                    }
                    this.errorEl.find('.kwfUp-message').stopFx().fadeIn({duration: 0.4});
                    this.errorEl.find('.kwfUp-arrow').stopFx().fadeIn({duration: 0.4});
                }).bind(this));
                field.el.on('mouseleave', (function() {
                    this.errorEl.find('.kwfUp-message').stopFx().fadeOut({duration: 0.2});
                    this.errorEl.find('.kwfUp-arrow').stopFx().fadeOut({duration: 0.2});
                }).bind(this));
            }
            field.errorEl.find('.kwfUp-message').update(r.errorFields[fieldName]);
            field.errorEl.clearOpacity();
            field.errorEl.fadeIn({
                endOpacity: 1 //TODO read from css (but that's hard for IE)
            });
        }
        if (firstField) {
            firstField.errorEl.find('.kwfUp-message').stopFx().fadeIn({duration: 0.4});
            firstField.errorEl.find('.kwfUp-arrow').stopFx().fadeIn({duration: 0.4});
            firstField.errorEl.find('.kwfUp-message').fadeOut.defer(4000, firstField.errorEl.find('.kwfUp-message'));
            firstField.errorEl.find('.kwfUp-arrow').fadeOut.defer(4000, firstField.errorEl.find('.kwfUp-arrow'));
        }

        if (r.errorMessages && r.errorMessages.length) {
            this._showErrorMessagesAbove(r.errorMessages, r);
        }
    },
    hideFieldError: function(field)
    {
        field.el.removeClass('kwfUp-kwfFieldError');
        if (field.errorEl && field.errorEl.isVisible() && !field.errorEl.fadingOut) {
            field.errorEl.hide();
            onReady.callOnContentReady(field.el, {newRender: false});
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

errorStyleRegistry.register('iconBubble', ErrorStyleIconBubble);
module.exports = ErrorStyleIconBubble;
