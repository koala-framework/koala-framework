Vps.Form.VpcLinkField = Ext.extend(Ext.form.TriggerField,
{
    triggerClass : 'x-form-search-trigger',
    readOnly: true,
    width: 200,
    onTriggerClick : function(){
        var win = Vps.Form.VpcLinkField.PagesWindow; //statische var, nur ein window erstellen??
        if (!win) {
            win = new Ext.Window({
                width: 535,
                height: 500,
                modal: true,
                closeAction: 'hide',
                title: trlVps('Select Page'),
                layout: 'fit',
                buttons: [{
                    text: trlVps('OK'),
                    handler: function() {
                        this.setValue(win.value);
                        win.hide();
                    },
                    scope: this
                },{
                    text: trlVps('Cancel'),
                    handler: function() {
                        win.hide();
                    },
                    scope: this
                }],
                items: new Vps.Auto.TreePanel({
                    controllerUrl: this.controllerUrl,
                    listeners: {
                        click: function(node) {
                            var n = node;
                            var name = '';
                            while (n.parentNode.parentNode) {
                                if (name) name += ' - ';
                                name += n.attributes.text;
                                n = n.parentNode;
                            }
                            win.value = {
                                id: node.id,
                                name: name
                            };
                        }
                    }
                })
            });
            Vps.Form.VpcLinkField.PagesWindow = win;
        }
        win.show();
        win.items.get(0).selectId(this.value);
    },

    getValue: function(value) {
        return this.value;
    },

    setValue: function(value) {
        if (typeof value.name != 'undefined') {
            Vps.Form.VpcLinkField.superclass.setValue.call(this, value.name);
            this.value = value.id;
        } else {
            Vps.Form.VpcLinkField.superclass.setValue.call(this, value);
            this.value = value;
        }
    }
});

Ext.reg('vpclink', Vps.Form.VpcLinkField);
