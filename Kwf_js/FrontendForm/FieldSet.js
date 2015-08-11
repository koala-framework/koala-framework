Kwf.onElementReady('div.kwfFormContainerFieldSet fieldset > legend > input[type="checkbox"]', function fieldSet(c)
{
    if (!c.dom.checked) {
        c.up('fieldset').addClass('kwfFormContainerFieldSetCollapsed');
    }
    c.on('click', function() {
        if (this.dom.checked) {
            this.up('fieldset').removeClass('kwfFormContainerFieldSetCollapsed');
        } else {
            this.up('fieldset').addClass('kwfFormContainerFieldSetCollapsed');
        }
    }, c);
});

Kwf.FrontendForm.FieldSet = Ext2.extend(Kwf.FrontendForm.Field, {
    initField: function() {
        var inp = this.el.child('fieldset > legend > input');
        if (inp) {
            inp.on('click', function() {
                this.fireEvent('change', this.getValue());
            }, this);
        }
    },
    getFieldName: function() {
        var inp = this.el.child('fieldset > legend > input');
        if (!inp) return null;
        return inp.dom.name;
    },
    getValue: function() {
        var inp = this.el.child('fieldset > legend > input');
        if (!inp) return null;
        return inp.dom.checked;
    },
    clearValue: function() {
        var inp = this.el.child('fieldset > legend > input');
        if (!inp) return;
        inp.dom.value = '';
    },
    setValue: function(value) {
        var inp = this.el.child('fieldset > legend > input');
        if (!inp) return;
        inp.dom.checked = value;
    }
});

Kwf.FrontendForm.fields['kwfFormContainerFieldSet'] = Kwf.FrontendForm.FieldSet;
