var $ = require('jQuery');
var fieldRegistry = require('kwf/frontend-form/field-registry');
var Field = require('kwf/frontend-form/field/field');
var kwfExtend = require('kwf/extend');

var Cards = kwfExtend(Field, {
    initField: function() {
        var config = this.form.getFieldConfig(this.getFieldName());
        var combobox = this.form.findField(config.combobox);
        combobox.el.find('input.submit').remove(); //remove non-js fallback
        combobox.el.on('change', (function() {
            this.el.find('.kwfUp-kwfFormContainerCard .kwfFormCard').addClass('inactive');
            this.el.find('.kwfUp-kwfFormContainerCard.kwfUp-'+combobox.getValue()+' .kwfFormCard').removeClass('inactive');
        }).bind(this));
    },
    getFieldName: function() {
        return this.el.data('fieldName');
    },
    getValue: function() {
        return null;
    },
    clearValue: function() {
    },
    setValue: function(value) {
    }
});

fieldRegistry.register('kwfFormContainerCards', Cards);
module.exports = Cards;
