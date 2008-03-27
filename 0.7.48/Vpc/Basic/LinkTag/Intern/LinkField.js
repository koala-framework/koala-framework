Vps.Form.VpcLinkField = Ext.extend(Ext.form.Field,
{
    defaultAutoCreate : {tag: "input", type: "hidden"},

    afterRender: function(){
        Vps.Form.ColorField.superclass.afterRender.call(this);
        var span = Ext.DomHelper.insertAfter(this.el, '<span></span>');
        this.pages = new Vps.Auto.TreePanel({
            controllerUrl: this.controllerUrl,
            renderTo: span,
            width: this.width
        });
        this.pages.on('click', function(node) {
            if (this.getValue() != node.id) {
                this.setValue(node.id);
            }
        }, this);

    },

    setValue: function(value){
        this.pages.selectId(value);
        Vps.Form.VpcLinkField.superclass.setValue.call(this, value);
    }
});

Ext.reg('vpclink', Vps.Form.VpcLinkField);
