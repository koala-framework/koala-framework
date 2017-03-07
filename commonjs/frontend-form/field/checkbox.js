var $ = require('jquery');
var fieldRegistry = require('kwf/commonjs/frontend-form/field-registry');
var Field = require('kwf/commonjs/frontend-form/field/field');
var kwfExtend = require('kwf/commonjs/extend');

var Checkbox = kwfExtend(Field, {
    initField: function() {
        this.el.find('input').each((function(index, input) {
            $(input).on('click', (function() {
                this.el.trigger('kwfUp-form-change', this.getValue());
            }).bind(this));
        }).bind(this));
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
