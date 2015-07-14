var fieldRegistry = require('kwf/frontend-form/field-registry');
var Field = require('kwf/frontend-form/field/field');
var kwfExtend = require('kwf/extend');
var trlKwf = require('kwf/trl').trlKwf;

var DateField = kwfExtend(Field, {
    initField: function() {
        if (!this.form.getFieldConfig(this.getFieldName()).hideTrigger) {
            var icon = this.el.find('.kwfFormFieldWrapper').append('<a class="icon" href="#"></a>');
            icon.on('click', function(ev) {
                ev.stopEvent();
                this.showPicker();
            }, this);
            this.el.find('input').on('focus', function(ev) {
                this.showPicker();
            }, this);
        }
        DateField.superclass.initField.call(this);
    },
    showPicker: function() {
        /*
        TODO commonjs
        if (!this.menu) {
            this.menu = new Ext2.menu.DateMenu({
                cls: 'kwfFrontendFormDatePicker',
                shadow: false,
                format: trlKwf('Y-m-d')
            });
            this.menu.on('select', function(menu, value) {
                this.el.find('input').get(0).value = value.format(trlKwf('Y-m-d'));
                this.el.trigger('kwf-form-change', this.el.find('input').get(0).value);
            }, this);
        }
        var value = Date.parseDate(this.getValue(), trlKwf('Y-m-d'));
        if (!value) value = new Date();
        this.menu.picker.setValue(value);
        this.menu.show(this.el.find('input'), 'bl');
        */
    }
});

fieldRegistry.register('kwfFormFieldDateField', DateField);
module.exports = DateField;
