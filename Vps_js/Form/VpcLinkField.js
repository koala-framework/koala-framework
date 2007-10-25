Vps.Form.VpcLinkField = Ext.extend(Ext.form.Hidden, {
    onRender : function(){
        Vps.Form.VpcLinkField.superclass.onRender.apply(this, arguments);

        var value = '';

        var span = '<span id="' + this.name + '_link' + '">' + value + '</span>';
        Ext.DomHelper.insertAfter(this.el, span);
        var span = '<span id="' + this.name + '_button' + '" />';
        Ext.DomHelper.insertAfter(this.el, span);


        this.button = new Ext.Button({
            renderTo: this.name + '_button',
            text: 'bar',
            handler: function(o, e) {
                this.pages = new Vps.Auto.TreePanel({
                    controllerUrl: '/admin/pages/'
                });

                this.window = new Ext.Window({
                    items: [this.pages],
                    width: 400,
                    height: 500
                });
                this.pages.on('loaded', function() {
                    this.pages.tree.on('dblclick', function(node, e) {
                        Ext.get(this.name + '_link').dom.innerHTML = node.id;
                        this.window.close();
                    }, this);
                }, this);

                this.window.show();
            },
            scope: this
        });

    }
});

Ext.reg('vpclink', Vps.Form.VpcLinkField);
