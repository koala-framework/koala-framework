var ErrorStyleAbstract = require('kwf/frontend-form/error-style/abstract');
var kwfExtend = require('kwf/extend');
var errorStyleRegistry = require('kwf/frontend-form/error-style-registry');

var ErrorStyleAbove = kwfExtend(ErrorStyleAbstract, {
    showErrors: function(r) {
        var errorMessages = r.errorMessages;
        for (var fieldName in r.errorFields) {
            errorMessages.push(r.errorFields[fieldName]);
            var field = this.form.findField(fieldName);
            field.el.addClass('kwfFieldError');
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
        field.el.removeClass('kwfFieldError');
        if (field.errorEl) field.errorEl.hide();
    }
});

errorStyleRegistry.register('above', ErrorStyleAbove);
module.exports = ErrorStyleAbove;
