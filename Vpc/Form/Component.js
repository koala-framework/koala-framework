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
                if (Vps.FrontendForm.fields[c]) {
                    fieldConstructor = Vps.FrontendForm.fields[c];
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
            
            var button = form.child('.button');
            button.down('.saving').show();
            button.down('.submit').hide();
            
            Ext.Ajax.request({
                url: config.controllerUrl + '/json-save',
                params: {
                    componentId: config.componentId
                },
                form: form.down('form'),
                success: function(response, options, r) {
                    
                    button.down('.saving').hide();
                    button.down('.submit').show();
                    
                    // remove and set error classes for fields
                    Ext.each(form.query('.vpsField'), function(field) {
                        Ext.fly(field).removeClass('vpsFieldError');
                    });
                    if (r.errorFields && r.errorFields.length) {
                        for (var i=0; i<r.errorFields.length; i++) {
                            var field = form.child('.' + r.errorFields[i]);
                            if (field) field.addClass('vpsFieldError');
                        }
                    }
                    
                    // remove and add error messages
                    var error = form.parent().down('.webFormError');
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
                
        });
    });
});
