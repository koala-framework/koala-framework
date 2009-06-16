Vps.Form.TreeSelect = Ext.extend(Ext.form.TriggerField,
{
    triggerClass : 'x-form-search-trigger',
    readOnly: true,
    width: 200,

    // mandatory parameters
    // controllerUrl (for the tree)

    // optional parameters
    // windowWidth, windowHeight
    // displayField

    onTriggerClick : function() {
        if (!this.selectWin) {
            this.selectWin = new Ext.Window({
                width: this.windowWidth || 535,
                height: this.windowHeight || 500,
                modal: true,
                closeAction: 'hide',
                title: trlVps('Please choose'),
                layout: 'fit',
                buttons: [{
                    text: trlVps('OK'),
                    handler: function() {
                        this.setValue(this.selectWin.value);
                        this.selectWin.hide();
                    },
                    scope: this
                }, {
                    text: trlVps('Cancel'),
                    handler: function() {
                        this.selectWin.hide();
                    },
                    scope: this
                }],
                items: new Vps.Auto.TreePanel({
                    controllerUrl: this.controllerUrl,
                    listeners: {
                        click: function(node) {
                            this.selectWin.value = {
                                id: node.id,
                                name: this.displayField ? node.attributes.data[this.displayField] : node.text
                            };
                        },
                        scope: this
                    }
                })
            });
        }
        this.selectWin.show();
        this.selectWin.items.get(0).selectId(this.value);
    },

    getValue: function() {
        return this.value;
    },

    setValue: function(value) {
        if (value && typeof value.name != 'undefined') {
            Vps.Form.TreeSelect.superclass.setValue.call(this, value.name);
            this.value = value.id;
        } else {
            Vps.Form.TreeSelect.superclass.setValue.call(this, value);
            this.value = value;
        }
    }

});
Ext.reg('treeselect', Vps.Form.TreeSelect);
