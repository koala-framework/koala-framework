var ErrorStyleAbove = require('kwf/frontend-form/error-style/above');
var kwfExtend = require('kwf/extend');
var errorStyleRegistry = require('kwf/frontend-form/error-style-registry');

var ErrorStyleBelowField = kwfExtend(ErrorStyleAbove, {
    showErrors: function(r) {

        for (var fieldName in r.errorFields) {
            var field = this.form.findField(fieldName);
            field.el.addClass('kwfFieldError');
            if (!field.errorEl) {
                if (field.el.up('.kwfFormContainerColumn')) {
                    field.errorEl = field.el.up('.kwfFormContainerColumn').up('.kwfFormContainerColumns')
                        .append('<div class="kwfFieldErrorMessage"></div>');
                } else {
                    field.errorEl = field.el.append('<div class="kwfFieldErrorMessage"></div>');
                }
            }
            field.errorEl.show();
            field.errorEl.update(r.errorFields[fieldName]);

        }
        if (r.errorMessages && r.errorMessages.length) {
            this._showErrorMessagesAbove(r.errorMessages, r);
        }
    },
    hideFieldError: function(field)
    {
        field.el.removeClass('kwfFieldError');
        if (field.errorEl) field.errorEl.hide();
    }
});

errorStyleRegistry.register('belowField', ErrorStyleBelowField);
module.exports = ErrorStyleBelowField;
