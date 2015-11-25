var fieldRegistry = require('kwf/frontend-form/field-registry');
var Field = require('kwf/frontend-form/field/field');
var kwfExtend = require('kwf/extend');

var DateSelect = kwfExtend(Field, {
    initField: function() {
        this.el.select('select').each((function(input) {
            $(input).on('change', (function() {
                this.el.trigger('kwfUp-form-change', this.getValue());
            }).bind(this));
        }).bind(this));
    },
    getFieldName: function() {
        var name = this.el.find('select').get(0).name;
        return name.substr(0, name.length-4);
    },
    getValue: function() {
        var value = {};
        this.el.select('select').each((function(index, input) {
            if (input.name.substr(-4) == '_day') {
                value['day'] = input.value;
            } else if (input.name.substr(-6) == '_month') {
                value['month'] = input.value;
            } else if (input.name.substr(-5) == '_year') {
                value['year'] = input.value;
            }
        }).bind(this));
        return value['year'] + '-' + value['month'] + '-' + value['day'];
    },
    clearValue: function() {
        this.el.select('select').each((function(index, input) {
            input.value='';
        }).bind(this));
    },
    // TODO: not yet implemented
    setValue: function(value) {
    }
});

fieldRegistry.register('kwfFormFieldDateSelect', DateSelect);
module.exports = DateSelect;
