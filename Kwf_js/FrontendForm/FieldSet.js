/* TODO commonjs
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
*/

Kwf.FrontendForm.FieldSet = Kwf.extend(Kwf.FrontendForm.Field, {
    initField: function() {
        var inp = this.el.find('fieldset > legend > input');
        if (inp) {
            inp.on('click', function() {
                this.el.trigger('kwf-form-change', this.getValue());
            }, this);
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

Kwf.FrontendForm.fields['kwfFormContainerFieldSet'] = Kwf.FrontendForm.FieldSet;
