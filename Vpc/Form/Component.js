//TODO move into own file, think about the name
Ext.namespace('Vpc.Form.Fields');
Vpc.Form.Fields.vpsField = function(fieldEl) {
    Vpc.Form.Fields.vpsField.superclass.constructor.call(this);
    this.el = fieldEl;
    this.el.enableDisplayMode();
    this.initField();
    this.addEvents('change');
};
Ext.extend(Vpc.Form.Fields.vpsField, Ext.util.Observable, {
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

//TODO move into own file, think about the name
Vpc.Form.Fields.vpsFormFieldRadio = Ext.extend(Vpc.Form.Fields.vpsField, {
    initField: function() {
        this.el.select('input').each(function(input) {
            input.on('click', function() {
                this.fireEvent('change', this.getValue());
            }, this);
        }, this);
    },
    getValue: function() {
        var ret = null;
        this.el.select('input').each(function(input) {
            if (input.dom.checked) {
                ret = input.dom.value;
            }
        }, this);
        return ret;
    },
});

//TODO move into own file, think about the name
Vpc.Form.Fields.vpsFormFieldSelect = Ext.extend(Vpc.Form.Fields.vpsField, {
    initField: function() {
        this.el.select('select').each(function(input) {
            input.on('click', function() {
                this.fireEvent('change', this.getValue());
            }, this);
        }, this);
    },
    getFieldName: function() {
        return this.el.child('select').dom.name;
    },
    getValue: function() {
        return this.el.child('select').dom.value;
    },
});

//TODO move into own file, think about the name
Vpc.Form.Fields.vpsFormFieldTextArea = Ext.extend(Vpc.Form.Fields.vpsField, {
    initField: function() {
        this.el.select('textarea').each(function(input) {
            input.on('keypress', function() {
                this.fireEvent('change', this.getValue());
            }, this);
        }, this);
    },
    getFieldName: function() {
        return this.el.child('textarea').dom.name;
    },
    getValue: function() {
        return this.el.child('textarea').dom.value;
    },
});

//TODO move into own file, think about the name
Vpc.Form.Fields.vpsFormFieldStatic = Ext.extend(Vpc.Form.Fields.vpsField, {
    initField: function() {
    },
    getValue: function() {
        return null;
    },
    getFieldName: function() {
        return null; //TODO?
    }
});

Vps.onContentReady(function() {
    Ext.select('.vpcForm', true).each(function(form) {
        var config = form.parent().down('.config', true);
        if (!config) return;
        config = Ext.decode(config.value);
        if (!config) return;

        //TODO there should be a proper Form class and object
        //but for now this should do...
        //TODO move into own file, think about the name
        var fields = [];
        form.select('.vpsField', true).each(function(fieldEl) {
            var classes = fieldEl.dom.className.split(' ');
            var fieldConstructor = false;
            classes.each(function (c) {
                if (Vpc.Form.Fields[c]) {
                    fieldConstructor = Vpc.Form.Fields[c];
                }
            }, this);
            if (fieldConstructor) {
                var field = new fieldConstructor(fieldEl);
                fields.push(field);
            }
        }, this);
        var findField = function(fieldName) {
            var ret = null;
            fields.each(function(f) {
                if (f.getFieldName() == fieldName) {
                    ret = f;
                    return true;
                }
            }, this);
            return ret;
        };
        if (config.hideForValue) {
            config.hideForValue.each(function(hideForValue) {
                var field = findField(hideForValue.field);
                field.on('change', function(v, f) {
                    if (v == hideForValue.value) {
                        findField(hideForValue.hide).hide();
                    } else {
                        findField(hideForValue.hide).show();
                    }
                }, this);

                //initialize on startup
                if (field.getValue() == hideForValue.value) {
                    findField(hideForValue.hide).hide();
                }
            }, this);
        }


        form.child('form button.submit').on('click', function(e) {
            e.stopEvent();
            
            var button = formDiv.child('.button');
            button.down('.saving').show();
            button.down('.submit').hide();
            
            Ext.Ajax.request({
                url: config.controllerUrl + '/json-save',
                params: {
                    componentId: config.componentId
                },
                form: formDiv.down('form'),
                success: function(response, options, r) {
                    
                    button.down('.saving').hide();
                    button.down('.submit').show();
                    
                    // remove and set error classes for fields
                    Ext.each(formDiv.query('.vpsField'), function(field) {
                        Ext.fly(field).removeClass('vpsFieldError');
                    });
                    if (r.errorFields && r.errorFields.length) {
                        for (var i=0; i<r.errorFields.length; i++) {
                            var field = formDiv.child('.' + r.errorFields[i]);
                            if (field) field.addClass('vpsFieldError');
                        }
                    }
                    
                    // remove and add error messages
                    var error = formDiv.parent().down('.webFormError');
                    if (error) error.remove();

                    if (r.errorMessages && r.errorMessages.length) {
                        var html = '<div class="webStandard vpcFormError webFormError">';
                        html += '<p class="error">' + r.errorPlaceholder + ':</p>';
                        html += '<ul>';
                        for (var i=0; i<r.errorMessages.length; i++) {
                            html += '<li>' + r.errorMessages[i] + '</li>';
                        }
                        html += '</ul>';
                        html += '</div>';
                        formDiv.parent().createChild(html, formDiv);
                    }
                    
                    // show success content
                    if (r.successContent) {
                        formDiv.parent().createChild(r.successContent);
                        formDiv.remove();
                    }
                },
                scope: this
            });
                
        });
    });
});
