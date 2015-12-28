var $ = require('jQuery');
var ErrorStyleAbove = require('kwf/frontend-form/error-style/above');
var kwfExtend = require('kwf/extend');
var errorStyleRegistry = require('kwf/frontend-form/error-style-registry');
var TextAreaField = require('kwf/frontend-form/field/text-area');

var ErrorStyleBubble = kwfExtend(ErrorStyleAbove, {
    showErrors: function(r) {

        for (var fieldName in r.errorFields) {
            var field = this.form.findField(fieldName);
            field.el.addClass('kwfUp-kwfFieldError');
            if (!field.errorEl) {
                field.errorEl = field.el.append('<div class="kwfUp-kwfFieldErrorBubble"></div>');
                field.errorEl.append('<div class="kwfUp-message"></div>');
                var closeButton = field.errorEl.append('<a class="kwfUp-closeButton"></a>');
                closeButton.on('click', function(ev) {
                    ev.preventDefault();
                    this.fadeOut();
                }, field.errorEl);
                if (field instanceof TextAreaField) {
                    field.errorEl.alignTo(field.el.find('textarea'), 'bl');
                } else if (field.el.find('input')) {
                    field.errorEl.alignTo(field.el.find('input'), 'bl');
                } else if (field.el.find('select')) {
                    field.errorEl.alignTo(field.el.find('select'), 'bl');
                } else {
                    field.errorEl.alignTo(field.el, 'bl');
                }
                field.errorEl.hide();
            }
            field.errorEl.find('.kwfUp-message').update(r.errorFields[fieldName]);
            field.errorEl.clearOpacity();
            field.errorEl.fadeIn({
                endOpacity: 0.8 //TODO read from css (but that's hard for IE)
            });
        }

        if (r.errorMessages && r.errorMessages.length) {
            this._showErrorMessagesAbove(r.errorMessages, r);
        }
    },
    hideFieldError: function(field)
    {
        field.el.removeClass('kwfUp-kwfFieldError');
        if (field.errorEl) field.errorEl.fadeOut();
    }
});

errorStyleRegistry.register('bubble', ErrorStyleBubble);
module.exports = ErrorStyleBubble;

