var fieldRegistry = require('kwf/commonjs/frontend-form/field-registry');
var _ = require('underscore');

var Field = function(fieldEl, form) {
    this.el = fieldEl;
    this.form = form;
    this.on('change', function(value) {
        if (this.form.errorStyle) {
            this.form.errorStyle.hideFieldError(this);
        }
    }, this);
};
Field.prototype = {
    initField: function() {
        var inp = this.el.find('input');
        if (inp) {
            inp.on('change', (function() {
                this.el.trigger('kwfUp-form-change', this.getValue());
            }).bind(this));
            inp.on('keydown', _.debounce((function() {
                this.el.trigger('kwfUp-form-change', this.getValue());
            }).bind(this), 1));
            this._initPlaceholder(this.el.find('input'));
        }
    },

    on: function(event, cb, scope)
    {
        if (typeof scope != 'undefined') cb = cb.bind(scope);
        this.el.on('kwfUp-form-'+event, cb);
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
        if (!inp.length) return null;
        return inp.get(0).name;
    },
    getValue: function() {
        var inp = this.el.find('input');
        if (!inp.length) return null;
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


fieldRegistry.register('kwfField', Field);
module.exports = Field;
