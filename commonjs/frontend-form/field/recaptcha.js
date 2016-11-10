var fieldRegistry = require('kwf/frontend-form/field-registry');
var Field = require('kwf/frontend-form/field/field');
var kwfExtend = require('kwf/extend');
var $ = require('jQuery');
var recaptcha = require('kwf/recaptcha');

var Recaptcha = kwfExtend(Field, {
    initField: function() {
        recaptcha((function() {
            var options = {
                'sitekey' : this.getField().getAttribute('data-site-key'),
                'callback' : (function() {
                    this.form.errorStyle.hideFieldError(this);
                }).bind(this)
            };
            window.grecaptcha.render(this.getField(), options);
        }).bind(this));
    },

    getFieldName: function() {
        return this.getField().getAttribute('data-recaptcha');
    },

    getField: function() {
        return this.el.find('div[data-recaptcha]').get(0);
    }
});

fieldRegistry.register('kwfFormFieldRecaptcha', Recaptcha);
module.exports = Recaptcha;
