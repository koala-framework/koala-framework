var $ = require('jquery');
var ErrorStyleAbove = require('kwf/commonjs/frontend-form/error-style/above');
var kwfExtend = require('kwf/commonjs/extend');
var errorStyleRegistry = require('kwf/commonjs/frontend-form/error-style-registry');
var onReady = require('kwf/commonjs/on-ready');
var elementIsVisible = require('kwf/commonjs/element/is-visible');

var ErrorStyleIconBubble = kwfExtend(ErrorStyleAbove, {
    showErrors: function(r) {

        var firstField = null;
        for (var fieldName in r.errorFields) {
            var field = this.form.findField(fieldName);
            if (!firstField) { firstField = field; }
            field.el.addClass('kwfUp-kwfFieldError');
            if (!field.errorEl) {
                field.errorEl = $('<div class="kwfUp-kwfFieldErrorIconBubble"></div>').appendTo(field.el.find('.kwfUp-kwfFormFieldWrapper'));
                field.errorEl.append('<div class="kwfUp-message"></div>');
                field.errorEl.append('<div class="kwfUp-arrow"></div>');
                field.errorEl.find('.kwfUp-message').hide();
                field.errorEl.find('.kwfUp-arrow').hide();
                field.errorEl.hide();

                field.el.on('mouseenter', (function() {
                    if (firstField) {
                        firstField.errorEl.find('.kwfUp-message').stop().fadeOut({duration: 400});
                        firstField.errorEl.find('.kwfUp-arrow').stop().fadeOut({duration: 400});
                    }
                    this.errorEl.find('.kwfUp-message').stop().fadeIn({duration: 400});
                    this.errorEl.find('.kwfUp-arrow').stop().fadeIn({duration: 400});
                }).bind(field));
                field.el.on('mouseleave', (function() {
                    this.errorEl.find('.kwfUp-message').stop().fadeOut({duration: 200});
                    this.errorEl.find('.kwfUp-arrow').stop().fadeOut({duration: 200});
                }).bind(field));
            }
            field.errorEl.find('.kwfUp-message').html(r.errorFields[fieldName]);
            field.errorEl.fadeIn();
        }
        if (firstField) {
            firstField.errorEl.find('.kwfUp-message').stop().fadeIn({duration: 400});
            firstField.errorEl.find('.kwfUp-arrow').stop().fadeIn({duration: 400});
            setTimeout(function() {
                firstField.errorEl.find('.kwfUp-message').fadeOut();
                firstField.errorEl.find('.kwfUp-arrow').fadeOut();
            }, 4000);
        }

        if (r.errorMessages && r.errorMessages.length) {
            this._showErrorMessagesAbove(r.errorMessages, r);
        }
    },
    hideFieldError: function(field)
    {
        field.el.removeClass('kwfUp-kwfFieldError');
        if (field.errorEl && elementIsVisible(field.errorEl) && !field.errorEl.fadingOut) {
            field.errorEl.hide();
            onReady.callOnContentReady(field.el, {newRender: false});
            field.errorEl.show();
            field.errorEl.fadeOut({
                complete: function() {
                    field.errorEl.hide();
                    field.errorEl.fadingOut = false;
                }
            });
            field.errorEl.fadingOut = true;
        }
    }
});

errorStyleRegistry.register('iconBubble', ErrorStyleIconBubble);
module.exports = ErrorStyleIconBubble;
