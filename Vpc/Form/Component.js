Vps.onContentReady(function() {
    var clicked = [];
    var btns = Ext.query('form button.submit', document);
    Ext.each(btns, function(btn) {
        btn = Ext.get(btn);
        btn.on('click', function(e) {

            var formDiv = Ext.get(this.findParent('.vpcForm'));
            var data = formDiv.parent().down('.data', true);
            if (!data) return;
            data = Ext.decode(data.value);
            if (!data) return;
            e.stopEvent();
            
            var button = formDiv.child('.button');
            button.down('.saving').show();
            button.down('.submit').hide();
            
            Ext.Ajax.request({
                url: data.controllerUrl + '/json-save',
                params: {
                    componentId: data.componentId
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
