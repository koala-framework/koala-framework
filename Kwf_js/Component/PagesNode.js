Ext2.namespace('Kwf.Component');
Kwf.Component.PagesNode = Ext2.extend(Kwf.Tree.Node, {
    onDblClick : function(e){
        e.preventDefault();
        this.fireEvent("dblclick", this.node, e);
    }
});
