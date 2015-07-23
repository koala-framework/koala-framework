var fieldRegistry = require('kwf/frontend-form/field-registry');
var Field = require('kwf/frontend-form/field/field');
var kwfExtend = require('kwf/extend');

var DateSelect = kwfExtend(Field, {
    initField: function() {
        this.el.select('select').each(function(input) {
            $(input).on('change', function() {
                this.el.trigger('kwf-form-change', this.getValue());
            }, this);
        }, this);
    },
    getFieldName: function() {
        var name = this.el.find('select').get(0).name;
        return name.substr(0, name.length-4);
    },
    getValue: function() {
        var value = {};
        this.el.select('select').each(function(input) {
            if (input.get(0).name.substr(-4) == '_day') {
                value['day'] = input.get(0).value;
            } else if (input.get(0).name.substr(-6) == '_month') {
                value['month'] = input.get(0).value;
            } else if (input.get(0).name.substr(-5) == '_year') {
                value['year'] = input.get(0).value;
            }
        }, this);
        return value['year'] + '-' + value['month'] + '-' + value['day'];
    },
    clearValue: function() {
        this.el.select('select').each(function(input) {
            input.get(0).value='';
        }, this);
    },
    // TODO: not yet implemented
    setValue: function(value) {
    }
});

fieldRegistry.register('kwfFormFieldDateSelect', DateSelect);
module.exports = DateSelect;
