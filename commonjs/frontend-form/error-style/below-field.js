var $ = require('jquery');
var ErrorStyleAbove = require('kwf/commonjs/frontend-form/error-style/above');
var kwfExtend = require('kwf/commonjs/extend');
var errorStyleRegistry = require('kwf/commonjs/frontend-form/error-style-registry');

var ErrorStyleBelowField = kwfExtend(ErrorStyleAbove, {
    showErrors: function(r) {

        for (var fieldName in r.errorFields) {
            var field = this.form.findField(fieldName);
            field.el.addClass('kwfUp-kwfFieldError');
            if (!field.errorEl) {
                field.errorEl = $('<div class="kwfUp-kwfFieldErrorMessage"></div>').appendTo(field.el);
            }
            field.errorEl.show();
            field.errorEl.html(r.errorFields[fieldName]);
        }
        if (r.errorMessages && r.errorMessages.length) {
            this._showErrorMessagesAbove(r.errorMessages, r);
        }
    },
    hideFieldError: function(field)
    {
        field.el.removeClass('kwfUp-kwfFieldError');
        if (field.errorEl) field.errorEl.hide();
    }
});

errorStyleRegistry.register('belowField', ErrorStyleBelowField);
module.exports = ErrorStyleBelowField;
