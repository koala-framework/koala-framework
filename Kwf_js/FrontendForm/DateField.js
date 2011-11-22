Kwf.FrontendForm.DateField = Ext.extend(Kwf.FrontendForm.Field, {
    initField: function() {
        var icon = this.el.createChild({
            tag: 'a',
            cls: 'icon',
            href: '#'
        });
        icon.alignTo(this.el.child('input'), 'tr');
        icon.on('click', function(ev) {
            ev.stopEvent();
            this.showPicker();
        }, this);
        this.el.child('input').on('focus', function(ev) {
            this.showPicker();
        }, this);
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
            }, this);
        }
        var value = Date.parseDate(this.getValue(), trlKwf('Y-m-d'));
        if (!value) value = new Date();
        this.menu.picker.setValue(value);
        this.menu.show(this.el.child('input'), 'bl');
    }
});

Kwf.FrontendForm.fields['kwfFormFieldDateField'] = Kwf.FrontendForm.DateField;
