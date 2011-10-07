Kwf.onContentReady(function() {
    Ext.select('.kwcForm', true).each(function(form) {
        var config = form.parent().down('.config', true);
        if (!config) return;
        config = Ext.decode(config.value);
        if (!config) return;

        //TODO there should be a proper Form class and object
        //but for now this should do...
        //TODO move into own file, think about the name
        var fields = [];
        form.select('.kwfField', true).each(function(fieldEl) {
            var classes = fieldEl.dom.className.split(' ');
            var fieldConstructor = false;
            classes.each(function (c) {
                if (Kwf.FrontendForm.fields[c]) {
                    fieldConstructor = Kwf.FrontendForm.fields[c];
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
            if (form.dontUseAjaxRequest) return;

            var button = form.child('.button');
            button.down('.saving').show();
            button.down('.submit').hide();
            
            Ext.Ajax.request({
                url: config.controllerUrl + '/json-save',
                ignoreErrors: true,
                params: {
                    componentId: config.componentId
                },
                form: form.down('form'),
                failure: function() {
                    //on failure try a plain old post of the form
                    form.dontUseAjaxRequest = true; //avoid endless recursion
                    button.down('.submit').dom.click();
                },
                success: function(response, options, r) {
                    
                    button.down('.saving').hide();
                    button.down('.submit').show();

                    // remove and set error classes for fields
                    fields.each(function(field) {
                        field.hideError();
                    }, this);
                    for(var fieldName in r.errorFields) {
                        var field = findField(fieldName);
                        field.showError(r.errorFields[fieldName]);
                    }

                    // remove and add error messages
                    var error = form.parent().down('.webFormError');
                    if (error) error.remove();

                    if (r.errorMessages && r.errorMessages.length) {
                        var html = '<div class="webStandard kwcFormError webFormError">';
                        html += '<p class="error">' + r.errorPlaceholder + ':</p>';
                        html += '<ul>';
                        for (var i=0; i<r.errorMessages.length; i++) {
                            html += '<li>' + r.errorMessages[i] + '</li>';
                        }
                        html += '</ul>';
                        html += '</div>';
                        form.parent().createChild(html, form);
                    }
                    
                    // show success content
                    if (r.successContent) {
                        form.parent().createChild(r.successContent);
                        form.remove();
                    }
                },
                scope: this
            });

            e.stopEvent();
        }, this);

    });
});
