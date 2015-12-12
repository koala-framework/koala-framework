var $ = require('jQuery');
var fieldRegistry = require('kwf/frontend-form/field-registry');
var Field = require('kwf/frontend-form/field/field');
var kwfExtend = require('kwf/extend');

var Select = kwfExtend(Field, {
    initField: function() {
        this.el.select('select').each((function(input) {
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
