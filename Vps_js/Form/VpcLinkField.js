Vps.Form.VpcLinkField = Ext.extend(Ext.form.Hidden, {
    onRender : function(){
        Vps.Form.VpcLinkField.superclass.onRender.apply(this, arguments);

    }
});

Ext.reg('vpclink', Vps.Form.VpcLinkField);
