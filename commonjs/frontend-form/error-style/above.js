var ErrorStyleAbstract = require('kwf/commonjs/frontend-form/error-style/abstract');
var kwfExtend = require('kwf/commonjs/extend');
var errorStyleRegistry = require('kwf/commonjs/frontend-form/error-style-registry');

var ErrorStyleAbove = kwfExtend(ErrorStyleAbstract, {
    showErrors: function(r) {
        var errorMessages = r.errorMessages;
        for (var fieldName in r.errorFields) {
            errorMessages.push(r.errorFields[fieldName]);
            var field = this.form.findField(fieldName);
            field.el.addClass('kwfUp-kwfFieldError');
        }
        if (errorMessages && errorMessages.length) {
            this._showErrorMessagesAbove(errorMessages, r);
        }
    },

    hideErrors: function()
    {
        ErrorStyleAbove.superclass.hideErrors.call(this);
        var error = this.form.el.parent().find('.kwfUp-webFormError');
        if (error) error.remove();
    },

    hideFieldError: function(field)
    {
        field.el.removeClass('kwfUp-kwfFieldError');
        if (field.errorEl) field.errorEl.hide();
    }
});

errorStyleRegistry.register('above', ErrorStyleAbove);
module.exports = ErrorStyleAbove;
