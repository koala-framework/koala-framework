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
            this._initPlaceholder(this.el.child('input'));
        }
    },

    _initPlaceholder: function(input)
    {
        var nativePlaceholderSupport = ('placeholder' in document.createElement('input'));
        if (!nativePlaceholderSupport) {
            this._placeholder = input.dom.getAttribute('placeholder');
            if (this._placeholder) {
                input.dom.value = this._placeholder;
                input.on('focus', function() {
                    if (input.getValue() == this._placeholder) {
                        input.dom.value = '';
                        input.removeClass('placeholderVisible');
                    }
                }, this);
                input.on('blur', function() {
                    if (input.getValue() == '') {
                        input.dom.value = this._placeholder;
                        input.addClass('placeholderVisible');
                    }
                }, this);
                this.form.on('beforeSubmit', function() {
                    if (input.dom.value == this._placeholder) {
                        input.dom.value = '';
                        input.removeClass('placeholderVisible');
                    }
                }, this);
                this.form.on('submitSuccess', function() {
                    if (input.dom.value == '') {
                        input.dom.value = this._placeholder;
                        input.addClass('placeholderVisible');
                    }
                }, this);
            }
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
        var ret = inp.dom.value;
        if (this._placeholder && ret == this._placeholder) ret = '';
        return ret;
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
