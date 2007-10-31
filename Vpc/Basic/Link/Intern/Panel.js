Ext.namespace('Vpc.Basic.Link');
Vpc.Basic.Link.Panel = Ext.extend(Vps.Auto.FormPanel, {
    initComponent: function()
    {
        Vpc.Basic.Link.Panel.superclass.initComponent.call(this);

        this.on('renderform', function()
        {
            this.targetName = this.getFormPanel().name + '_target';
            this.targetField = this.getForm().findField(this.targetName);
            this.relField =  this.getForm().findField(this.getFormPanel().name + '_rel');
            this.typeField = this.getForm().findField(this.getFormPanel().name + '_type');

            this.typeField.on('beforeselect', function(typeField) {
                type = this.typeField.getValue();
                this.rel[type] = this.relField.getValue();
                this.target[type] = this.targetField.getValue();
            }, this);
            this.typeField.on('select', this.changeType, this);
        }, this);

        this.on('renderform', function()
        {
            this.rel = {
                intern : '',
                extern: 'extern',
                mailto: 'mailto'
            };
            this.target = {
                intern : this.targetField.getValue(),
                extern: this.targetField.getValue(),
                mailto: this.targetField.getValue()
            };
            this.changeType(this.typeField);
        }, this, {delay: 10});
    },

    changeType : function(typeField, record, index)
    {
        if (this.span == undefined) {
            var span = '<span id="' + this.targetName + '_content' + '"></span>';
            this.span = Ext.DomHelper.insertAfter(this.targetField.el, span);
        } else {
            Ext.DomHelper.overwrite(this.span, '');
        }

        var type = typeField.getValue();
        if (type == 'intern') {
            this.showIntern(this.targetName, this.targetField, this.relField);
        } else if (type == 'extern') {
            this.showExtern(this.targetName, this.targetField, this.relField);
        } else if (type == 'mailto') {
            this.showMailto(this.targetName, this.targetField, this.relField);
        }
    },

    showIntern : function(targetName, targetField, relField)
    {
        relField.setValue(this.rel.intern);
        targetField.setValue(this.target.intern);

        var span = '<span id="' + targetName + '_button' + '" />';
        Ext.DomHelper.append(this.span, span);

        var button = new Ext.Button({
            renderTo: targetName + '_button',
            text: 'Select Target',
            handler: function(o, e) {

                this.window = new Ext.Window({
                    width: 400,
                    height: 500,
                    modal: true,
                    autoScroll: true,
                    title: 'Select Page with doubleclick'
                });
                this.pages = new Vps.Auto.TreePanel({
                    controllerUrl: this.pagesControllerUrl,
                    openedUrl: targetField.getValue()
                });
                this.window.add(this.pages);
                this.pages.on('loaded', function() {
                    this.pages.tree.on('load', function(node) {
                        if (node.attributes.data != undefined &&
                            targetField.getValue() == node.attributes.data.url
                        ) {
                            node.select();
                        }
                        return true;
                    }, this);
                    this.pages.tree.on('dblclick', function(node, e) {
                        targetField.setValue(node.attributes.data.url);
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
        relField.setValue(this.rel.extern);
        targetField.setValue(this.target.extern);
    },

    showMailto : function(targetName, targetField, relField)
    {
        relField.setValue(this.rel.mailto);
        targetField.setValue(this.target.mailto);
    }

});
