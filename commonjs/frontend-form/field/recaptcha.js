var fieldRegistry = require('kwf/commonjs/frontend-form/field-registry');
var Field = require('kwf/commonjs/frontend-form/field/field');
var kwfExtend = require('kwf/commonjs/extend');
var $ = require('jquery');
var recaptchaLoader = require('kwf/commonjs/recaptcha-loader');

var Recaptcha = kwfExtend(Field, {
    initField: function() {
        recaptchaLoader((function() {
            var options = {
                'sitekey' : this.getField().getAttribute('data-site-key'),
                'callback' : (function() {
                    if (this.form.errorStyle) {
                        this.form.errorStyle.hideFieldError(this);
                    }
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
