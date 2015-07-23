var $ = require('jQuery');
var fieldRegistry = require('kwf/frontend-form/field-registry');
var Field = require('kwf/frontend-form/field/field');
var kwfExtend = require('kwf/extend');

var Checkbox = kwfExtend(Field, {
    initField: function() {
        this.el.select('input').each(function(input) {
            $(input).on('click', function() {
                this.el.trigger('kwf-form-change', this.getValue());
            }, this);
        }, this);
    },
    clearValue: function() {
        var inp = this.el.find('input');
        inp.get(0).checked = false;
    },
    setValue: function(value) {
        var inp = this.el.find('input');
        inp.get(0).checked = !!value;
    },
    getValue: function(value) {
        var inp = this.el.find('input');
        return inp.get(0).checked;
    }
});

fieldRegistry.register('kwfFormFieldCheckbox', Checkbox);
module.exports = Checkbox;
