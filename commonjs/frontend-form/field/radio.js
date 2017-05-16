var $ = require('jquery');
var fieldRegistry = require('kwf/commonjs/frontend-form/field-registry');
var Field = require('kwf/commonjs/frontend-form/field/field');
var kwfExtend = require('kwf/commonjs/extend');

var Radio = kwfExtend(Field, {
    initField: function() {
        this.el.find('input').each((function(index, input) {
            $(input).on('click', (function() {
                this.el.trigger('kwfUp-form-change', this.getValue());
            }).bind(this));
        }).bind(this));
    },
    getValue: function() {
        var ret = null;
        this.el.find('input').each(function(index, input) {
            if (input.checked) {
                ret = input.value;
            }
        });
        return ret;
    },
    clearValue: function() {
        this.el.find('input').each(function(index, input) {
            input.checked = false;
        });
    },
    setValue: function(value) {
        this.el.find('input').each(function(index, input) {
            if (input.value == value) {
                input.checked = true;
            } else {
                input.checked = false;
            }
        });
    }
});

fieldRegistry.register('kwfFormFieldRadio', Radio);
module.exports = Radio;
