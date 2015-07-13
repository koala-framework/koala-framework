var fieldRegistry = require('kwf/frontend-form/field-registry');
var Field = require('kwf/frontend-form/field/field');
var kwfExtend = require('kwf/extend');
var onReady = require('kwf/on-ready');

onReady.onRender('div.kwfFormContainerFieldSet fieldset > legend > input', function fieldSet(c)
{
    if (!c.get(0).checked) {
        c.up('fieldset').addClass('kwfFormContainerFieldSetCollapsed');
    }
    c.on('click', function() {
        if (this.get(0).checked) {
            this.up('fieldset').removeClass('kwfFormContainerFieldSetCollapsed');
        } else {
            this.up('fieldset').addClass('kwfFormContainerFieldSetCollapsed');
        }
    }, c);
});

var FieldSet = kwfExtend(Field, {
    initField: function() {
        var inp = this.el.find('fieldset > legend > input');
        if (inp) {
            inp.on('click', (function() {
                this.el.trigger('kwf-form-change', this.getValue());
            }).bind(this));
        }
    },
    getFieldName: function() {
        var inp = this.el.find('fieldset > legend > input');
        if (!inp) return null;
        return inp.get(0).name;
    },
    getValue: function() {
        var inp = this.el.find('fieldset > legend > input');
        if (!inp) return null;
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
