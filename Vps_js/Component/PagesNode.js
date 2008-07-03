Ext.namespace('Vps.Component');
Vps.Component.PagesNode = Ext.extend(Vps.Tree.Node, {
    onDblClick : function(e){
        e.preventDefault();
        this.fireEvent("dblclick", this.node, e);
    }
});
