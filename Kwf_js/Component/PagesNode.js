Ext.namespace('Kwf.Component');
Kwf.Component.PagesNode = Ext.extend(Kwf.Tree.Node, {
    onDblClick : function(e){
        e.preventDefault();
        this.fireEvent("dblclick", this.node, e);
    }
});
