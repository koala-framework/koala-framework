Kwf.onContentReady(function(el, param) {
    if (!param.newRender) return false;
    Ext.select('.kwcForm', true, el).each(function(form) {
        if (!form.kwcForm) {
            form.kwcForm = new Kwc.Form.Component(form);
        }
    });
});
Ext.ns('Kwc.Form');
Kwc.Form.Component = function(form)
{
    this.addEvents('submitSuccess');
    this.el = form;
    var config = form.parent().down('.config', true);
    if (!config) return;
    config = Ext.decode(config.value);
    if (!config) return;
    this.config = config;

    this.fields = [];
    form.select('.kwfField', true).each(function(fieldEl) {
        var classes = fieldEl.dom.className.split(' ');
        var fieldConstructor = false;
        classes.each(function (c) {
            if (Kwf.FrontendForm.fields[c]) {
                fieldConstructor = Kwf.FrontendForm.fields[c];
            }
        }, this);
        if (fieldConstructor) {
            var field = new fieldConstructor(fieldEl, this);
            this.fields.push(field);
        }
    }, this);

    if (this.config.hideForValue) {
        this.config.hideForValue.each(function(hideForValue) {
            var field = this.findField(hideForValue.field);
            field.on('change', function(v, f) {
                if (v == hideForValue.value) {
                    this.findField(hideForValue.hide).hide();
                } else {
                    this.findField(hideForValue.hide).show();
                }
            }, this);

            //initialize on startup
            if (field.getValue() == hideForValue.value) {
                this.findField(hideForValue.hide).hide();
            }
        }, this);
    }

    if (this.config.useAjaxRequest) {
        var button = form.child('form button.submit');
        if (button) {
            button.on('click', this.onSubmit, this);
        }
    }

    this.errorStyle = new Kwf.FrontendForm.errorStyles[this.config.errorStyle](this);
};
Ext.extend(Kwc.Form.Component, Ext.util.Observable, {
    getFieldConfig: function(fieldName)
    {
        if (this.config.fieldConfig[fieldName]) {
            return this.config.fieldConfig[fieldName];
        }
        return {};
    },
    findField: function(fieldName) {
        var ret = null;
        this.fields.each(function(f) {
            if (f.getFieldName() == fieldName) {
                ret = f;
                return true;
            }
        }, this);
        return ret;
    },
    onSubmit: function(e) {
        if (this.dontUseAjaxRequest) return;

        var button = this.el.child('.button');
        button.down('.saving').show();
        button.down('.submit').hide();

        this.errorStyle.hideErrors();

        Ext.Ajax.request({
            url: this.config.controllerUrl + '/json-save',
            ignoreErrors: true,
            params: {
                componentId: this.config.componentId
            },
            form: this.el.down('form'),
            failure: function() {
                //on failure try a plain old post of the form
                this.dontUseAjaxRequest = true; //avoid endless recursion
                button.down('.submit').dom.click();
            },
            success: function(response, options, r) {

                this.errorStyle.showErrors({
                    errorFields: r.errorFields,
                    errorMessages: r.errorMessages
                });

                var hasErrors = false;
                if (r.errorMessages && r.errorMessages.length) {
                    hasErrors = true;
                }
                for(var fieldName in r.errorFields) {
                    hasErrors = true;
                }

                // show success content
                if (r.successContent) {
                    var el = this.el.parent().createChild(r.successContent);
                    if (this.config.hideFormOnSuccess) {
                        this.el.remove();
                    } else {
                        (function(el) {
                            el.remove();
                            Kwf.callOnContentReady(this.el.dom);
                        }).defer(5000, this, [el]);
                    }
                    Kwf.callOnContentReady(el.dom, {newRender: true});
                } else if (r.successUrl) {
                    document.location.href = r.successUrl;
                }
                
                if (!r.successUrl) {
                    button.down('.saving').hide();
                    button.down('.submit').show();
                }

                if (!hasErrors) {
                    this.fireEvent('submitSuccess', this, r);
                }
            },
            scope: this
        });

        e.stopEvent();
    }
});
