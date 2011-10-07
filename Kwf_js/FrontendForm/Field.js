Ext.namespace('Kwf.FrontendForm');
Kwf.FrontendForm.Field = function(fieldEl) {
    Kwf.FrontendForm.Field.superclass.constructor.call(this);
    this.el = fieldEl;
    this.el.enableDisplayMode();
    this.initField();
    this.addEvents('change');
};
Ext.extend(Kwf.FrontendForm.Field, Ext.util.Observable, {
    initField: function() {
        var inp = this.el.child('input');
        if (inp) {
            inp.on('change', function() {
                this.fireEvent('change', this.getValue());
            }, this);
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
    hide: function() {
        this.el.hide();
    },
    show: function() {
        this.el.show();
    },
    hideError: function()
    {
        if (this.errorEl) this.errorEl.hide();
    },
    showError: function(msg) {
        this.el.addClass('kwfFieldError');
        if (!this.errorEl) {
            this.errorEl = this.el.createChild({
                cls: 'kwfFieldErrorMessage'
            });
        }
        this.errorEl.show();
        this.errorEl.update(msg);
    }
});

Kwf.FrontendForm.fields = {};
Kwf.FrontendForm.fields['kwfField'] = Kwf.FrontendForm.Field;
