var fieldRegistry = require('kwf/frontend-form/field-registry');
var Field = require('kwf/frontend-form/field/field');
var kwfExtend = require('kwf/extend');

Recaptcha = kwfExtend(Field, {
    getFieldName: function() {
        return this.el.find('div[data-fieldname]').get(0).getAttribute('data-fieldname');
    },
    onError: function(message) {
        Recaptcha.reload(); //there can be only a single Recaptcha on the page
    }
});

fieldRegistry.register('kwfFormFieldRecaptcha', Recaptcha);
module.exports = Recaptcha;
