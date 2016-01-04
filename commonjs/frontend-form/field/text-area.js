var $ = require('jQuery');
var fieldRegistry = require('kwf/frontend-form/field-registry');
var Field = require('kwf/frontend-form/field/field');
var kwfExtend = require('kwf/extend');

var TextArea = kwfExtend(Field, {
    initField: function() {
        var input = this.el.find('textarea');
        if (input) {
            input.on('keypress', (function () {
                this.el.trigger('kwfUp-form-change', this.getValue());
            }).bind(this));
            this._initPlaceholder(input);
        }
    },
    getFieldName: function() {
        return this.el.find('textarea').get(0).name;
    },
    getValue: function() {
        return this.el.find('textarea').get(0).value;
    },
    clearValue: function() {
        this.el.find('textarea').get(0).value = '';
    },
    clearValue: function(value) {
        this.el.find('textarea').get(0).value = value;
    }
});

fieldRegistry.register('kwfFormFieldTextArea', TextArea);
module.exports = TextArea;
