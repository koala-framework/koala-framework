Ext.namespace('Vps.FrontendForm');
Vps.FrontendForm.Field = function(fieldEl) {
    Vps.FrontendForm.Field.superclass.constructor.call(this);
    this.el = fieldEl;
    this.el.enableDisplayMode();
    this.initField();
    this.addEvents('change');
};
Ext.extend(Vps.FrontendForm.Field, Ext.util.Observable, {
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
    }
});

Vps.FrontendForm.fields['vpsField'] = Vps.FrontendForm.Field;
