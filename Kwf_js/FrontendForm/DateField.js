Kwf.FrontendForm.DateField = Ext.extend(Kwf.FrontendForm.Field, {
    initField: function() {
        if (!this.form.getFieldConfig(this.getFieldName()).hideTrigger) {
            var icon = this.el.child('.kwfFormFieldWrapper').createChild({
                tag: 'a',
                cls: 'icon',
                href: '#'
            });
            icon.on('click', function(ev) {
                ev.stopEvent();
                this.showPicker();
            }, this);
            this.el.child('input').on('focus', function(ev) {
                this.showPicker();
            }, this);
        }
    },
    showPicker: function() {
        if (!this.menu) {
            this.menu = new Ext.menu.DateMenu({
                cls: 'kwfFrontendFormDatePicker',
                shadow: false,
                format: trlKwf('Y-m-d')
            });
            this.menu.on('select', function(menu, value) {
                this.el.child('input').dom.value = value.format(trlKwf('Y-m-d'));
                this.fireEvent('change', this.el.child('input').dom.value);
            }, this);
        }
        var value = Date.parseDate(this.getValue(), trlKwf('Y-m-d'));
        if (!value) value = new Date();
        this.menu.picker.setValue(value);
        this.menu.show(this.el.child('input'), 'bl');
    }
});

Kwf.FrontendForm.fields['kwfFormFieldDateField'] = Kwf.FrontendForm.DateField;
