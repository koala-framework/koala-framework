var fieldRegistry = require('kwf/frontend-form/field-registry');
var Field = require('kwf/frontend-form/field/field');
var kwfExtend = require('kwf/extend');

var Cards = kwfExtend(Field, {
    initField: function() {
        var config = this.form.getFieldConfig(this.getFieldName());
        var combobox = this.form.findField(config.combobox);
        combobox.el.find('input.submit').remove(); //remove non-js fallback
        combobox.on('change', (function() {
            this.el.select('.kwfFormContainerCard .kwfFormCard').addClass('inactive');
            this.el.select('.kwfFormContainerCard.'+combobox.value+' .kwfFormCard').removeClass('inactive');
        }).bind(this));
    },
    getFieldName: function() {
        var classNames = this.el.get(0).className.split(' ');
        return classNames[classNames.length-1];
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
