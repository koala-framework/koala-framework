Kwf.namespace('Kwf.FrontendForm');
Kwf.FrontendForm.Field = function(fieldEl, form) {
    Kwf.FrontendForm.Field.superclass.constructor.call(this);
    this.el = fieldEl;
    this.form = form;
    this.addEvents('change');
    this.on('change', function(value) {
        this.form.errorStyle.hideFieldError(this);
    }, this);
};
Kwf.FrontendForm.Field.prototype = {
    initField: function() {
        var inp = this.el.find('input');
        if (inp) {
            inp.on('change', function() {
                this.el.trigger('kwf-form-change', this.getValue());
            }, this);
            inp.on('keydown', function() {
                this.el.trigger('kwf-form-change', this.getValue());
            }, this, { delay: 1 });
            this._initPlaceholder(this.el.find('input'));
        }
    },

    on: function(event, cb, scope)
    {
        if (typeof scope != 'undefined') cb.bind(scope);
        this.el.on(event, cb);
    },

    _initPlaceholder: function(input)
    {
        var nativePlaceholderSupport = ('placeholder' in document.createElement('input'));
        if (!nativePlaceholderSupport) {
            this._placeholder = input.get(0).getAttribute('placeholder');
            if (this._placeholder) {
                if (!input.get(0).value) {
                    input.get(0).value = this._placeholder;
                    input.addClass('placeholderVisible');
                }
                input.on('focus', function() {
                    if (input.getValue() == this._placeholder) {
                        input.get(0).value = '';
                        input.removeClass('placeholderVisible');
                    }
                }, this);
                input.on('blur', function() {
                    if (input.getValue() == '') {
                        input.get(0).value = this._placeholder;
                        input.addClass('placeholderVisible');
                    }
                }, this);
                this.form.on('beforeSubmit', function() {
                    if (input.get(0).value == this._placeholder) {
                        input.get(0).value = '';
                        input.removeClass('placeholderVisible');
                    }
                }, this);
                this.form.on('submitSuccess', function() {
                    if (input.get(0).value == '') {
                        input.get(0).value = this._placeholder;
                        input.addClass('placeholderVisible');
                    }
                }, this);
            }
        }
    },
    getFieldName: function() {
        var inp = this.el.find('input');
        if (!inp) return null;
        return inp.get(0).name;
    },
    getValue: function() {
        var inp = this.el.find('input');
        if (!inp) return null;
        var ret = inp.get(0).value;
        if (this._placeholder && ret == this._placeholder) ret = '';
        return ret;
    },
    clearValue: function() {
        var inp = this.el.find('input');
        inp.get(0).value = '';
    },
    setValue: function(value) {
        var inp = this.el.find('input');
        inp.get(0).value = value;
    },
    hide: function() {
        this.el.hide();
    },
    show: function() {
        this.el.show();
    },
    onError: function(message) {
    }
};

Kwf.FrontendForm.fields = {};
Kwf.FrontendForm.fields['kwfField'] = Kwf.FrontendForm.Field;
