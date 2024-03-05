var fieldRegistry = require('kwf/commonjs/frontend-form/field-registry');
var Field = require('kwf/commonjs/frontend-form/field/field');
var kwfExtend = require('kwf/commonjs/extend');
var onReady = require('kwf/commonjs/on-ready');
require('kwf/commonjs/frontend-form/field/field-set.css');

onReady.onRender('div.kwfUp-kwfFormContainerFieldSet fieldset > legend > input[type="checkbox"]', function fieldSet(c)
{
    if (!c.get(0).checked) {
        c.closest('fieldset').addClass('kwfFormContainerFieldSetCollapsed');
    }
    c.on('click', (function() {
        if (this.get(0).checked) {
            this.closest('fieldset').removeClass('kwfFormContainerFieldSetCollapsed');
        } else {
            this.closest('fieldset').addClass('kwfFormContainerFieldSetCollapsed');
        }
    }).bind(c));
});

var FieldSet = kwfExtend(Field, {
    initField: function() {
        var inp = this.el.find('fieldset > legend > input');
        if (inp) {
            inp.on('click', (function() {
                this.el.trigger('kwfUp-form-change', this.getValue());
            }).bind(this));
        }
    },
    getFieldName: function() {
        var inp = this.el.find('fieldset > legend > input');
        if (!inp.length) return null;
        return inp.get(0).name;
    },
    getValue: function() {
        var inp = this.el.find('fieldset > legend > input');
        if (!inp.length) return null;
        return inp.get(0).checked;
    },
    clearValue: function() {
        var inp = this.el.find('fieldset > legend > input');
        if (!inp) return;
        inp.get(0).value = '';
    },
    setValue: function(value) {
        var inp = this.el.find('fieldset > legend > input');
        if (!inp) return;
        inp.get(0).checked = value;
    }
});

fieldRegistry.register('kwfFormContainerFieldSet', FieldSet);
module.exports = FieldSet;
