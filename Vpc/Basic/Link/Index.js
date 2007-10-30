Ext.namespace('Vpc.Basic.Link');
Vpc.Basic.Link.Index = Ext.extend(Vps.Auto.FormPanel, {
    initComponent: function()
    {
        Vpc.Basic.Link.Index.superclass.initComponent.call(this);

        this.on('renderform', function()
        {
            var selectField = this.getForm().findField(this.getFormPanel().name + '_type');
            selectField.on('select', this.changeType, this);
        }, this);
        this.on('renderform', function()
        {
            var selectField = this.getForm().findField(this.getFormPanel().name + '_type');
            this.changeType(selectField);
        }, this, {delay: 10});
    },

    changeType : function(selectField)
    {
        var targetName = this.getFormPanel().name + '_target';
        var targetField = this.getForm().findField(targetName);
        var relField =  this.getForm().findField(this.getFormPanel().name + '_rel');

        if (this.span == undefined) {
            this.originalType = selectField.getValue();
            this.originalTarget = targetField.getValue();
            this.originalRel = relField.getValue();
            var span = '<span id="' + targetName + '_content' + '"></span>';
            this.span = Ext.DomHelper.insertAfter(targetField.el, span);
        } else {
            targetField.show();
            targetField.setDisabled(false);
            Ext.DomHelper.overwrite(this.span, '');
        }

        var value = selectField.getValue();
        if (value == 'intern') {
            this.showIntern(targetName, targetField, relField);
        } else if (value == 'extern') {
            this.showExtern(targetName, targetField, relField);
        } else if (value == 'mailto') {
            this.showMailto(targetName, targetField, relField);
        }
    },

    showIntern : function(targetName, targetField, relField)
    {
        relField.setValue('extern');

        var textField = new Ext.form.TextField({
            'fieldLabel'    : 'foo',
            'width'         : 500,
            'readOnly'      : true,
            'renderTo'      : this.span
        })

        var span = '<span id="' + targetName + '_button' + '" />';
        Ext.DomHelper.append(this.span, span);

        targetField.hide();



        var button = new Ext.Button({
            renderTo: targetName + '_button',
            text: 'Select Target',
            handler: function(o, e) {

                this.window = new Ext.Window({
                    width: 400,
                    height: 500,
                    modal: true
                });
                this.pages = new Vps.Auto.TreePanel({
                    controllerUrl: this.pagesControllerUrl,
                    openedId: targetField.getValue()
                });
                this.window.add(this.pages);
                this.pages.on('loaded', function() {
                    this.pages.tree.on('dblclick', function(node, e) {
                        targetField.setValue(node.id);
                        var text = '';
                        var n = node;
                        while (n && n.text != 'Root') {
                            if (text != '') { text = ' -> ' + text; }
                            text = n.text + text;
                            n = n.parentNode;
                        }
                        textField.setValue(text);
                        this.window.close();
                    }, this);
                }, this);

                this.window.show();
            },
            scope: this
        });

    },

    showExtern : function(targetName, targetField, relField)
    {
        targetField.setValue('foo');
        relField.setValue('extern');
    },

    showMailto : function(targetName, targetField)
    {
    }

});
