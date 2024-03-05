var $ = require('jquery');
var fieldRegistry = require('kwf/commonjs/frontend-form/field-registry');
var Field = require('kwf/commonjs/frontend-form/field/field');
var kwfExtend = require('kwf/commonjs/extend');
require('kwf/commonjs/frontend-form/field/cards.css');

var Cards = kwfExtend(Field, {
    initField: function() {
        var config = this.form.getFieldConfig(this.getFieldName());
        var combobox = this.form.findField(config.combobox);
        combobox.el.find('input.submit').remove(); //remove non-js fallback
        combobox.el.on('change', (function() {
            this.el.find('.kwfUp-kwfFormContainerCard .kwfFormCard').addClass('inactive');
            this.el.find('.kwfUp-kwfFormContainerCard.kwfUp-'+combobox.getValue()+' .kwfFormCard').each(function(index, card) {
                if ($(card).closest('.kwfUp-kwfFormContainerCards')[0] == this.el[0]) {
                    $(card).removeClass('inactive');
                }
            }.bind(this));
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
