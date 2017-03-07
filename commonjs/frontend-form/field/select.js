var $ = require('jquery');
var fieldRegistry = require('kwf/commonjs/frontend-form/field-registry');
var Field = require('kwf/commonjs/frontend-form/field/field');
var kwfExtend = require('kwf/commonjs/extend');

var Select = kwfExtend(Field, {
    initField: function() {
        this.el.find('select').each((function(index, input) {
            $(input).on('change', (function() {
                this.el.trigger('kwfUp-form-change', this.getValue());
            }).bind(this));
        }).bind(this));
    },
    getFieldName: function() {
        return this.el.find('select').get(0).name;
    },
    getValue: function() {
        return this.el.find('select').get(0).value;
    },
    clearValue: function() {
        this.el.find('select').get(0).value = '';
    },
    setValue: function(value) {
        this.el.find('select').get(0).value = value;
    }
});

fieldRegistry.register('kwfFormFieldSelect', Select);
module.exports = Select;
