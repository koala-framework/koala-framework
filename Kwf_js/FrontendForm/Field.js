Ext.namespace('Kwf.FrontendForm');
Kwf.FrontendForm.Field = function(fieldEl, form) {
    Kwf.FrontendForm.Field.superclass.constructor.call(this);
    this.el = fieldEl;
    this.el.enableDisplayMode();
    this.form = form;
    this.addEvents('change');
    this.on('change', function(value) {
        this.form.errorStyle.hideFieldError(this);
    }, this);
};
Ext.extend(Kwf.FrontendForm.Field, Ext.util.Observable, {
    initField: function() {
        var inp = this.el.child('input');
        if (inp) {
            inp.on('change', function() {
                this.fireEvent('change', this.getValue());
            }, this);
            inp.on('keydown', function() {
                this.fireEvent('change', this.getValue());
            }, this, { delay: 1 });
        }
    },
    getFieldName: function() {
        var inp = this.el.child('input');
        if (!inp) return null;
        return inp.dom.name;
    },
    getValue: function() {
        var inp = this.el.child('input');
        if (!inp) return null;
        return inp.dom.value;
    },
    clearValue: function() {
        var inp = this.el.child('input');
        inp.dom.value = '';
    },
    setValue: function(value) {
        var inp = this.el.child('input');
        inp.dom.value = value;
    },
    hide: function() {
        this.el.hide();
    },
    show: function() {
        this.el.show();
    }
});

Kwf.FrontendForm.fields = {};
Kwf.FrontendForm.fields['kwfField'] = Kwf.FrontendForm.Field;
