Ext2.namespace('Kwf.Tree');
Kwf.Tree.Node = Ext2.extend(Ext2.tree.TreeNodeUI, {
    initEvents : function(){
        Kwf.Tree.Node.superclass.initEvents.call(this);
        this.node.ui.iconNode.style.backgroundImage = 'url(' + this.node.attributes.bIcon + ')';
    },
    onDblClick : function(e){
        e.preventDefault();
        this.fireEvent("dblclick", this.node, e);
    }
});
