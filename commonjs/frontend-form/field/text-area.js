var fieldRegistry = require('kwf/frontend-form/field-registry');
var Field = require('kwf/frontend-form/field/field');
var kwfExtend = require('kwf/extend');

var TextArea = kwfExtend(Field, {
    initField: function() {
        this.el.select('textarea').each(function(input) {
            $(input).on('keypress', function() {
                this.el.trigger('kwf-form-change', this.getValue());
            }, this);
            this._initPlaceholder(input);
        }, this);
    },
    getFieldName: function() {
        return this.el.find('textarea').get(0).name;
    },
    getValue: function() {
        return this.el.find('textarea').get(0).value;
    },
    clearValue: function() {
        this.el.select('textarea').get(0).value = '';
    },
    clearValue: function(value) {
        this.el.select('textarea').get(0).value = value;
    }
});

fieldRegistry.register('kwfFormFieldTextArea', TextArea);
module.exports = TextArea;
