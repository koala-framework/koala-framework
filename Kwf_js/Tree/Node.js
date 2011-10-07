Ext.namespace('Vps.Tree');
Vps.Tree.Node = Ext.extend(Ext.tree.TreeNodeUI, {
    initEvents : function(){
        Vps.Tree.Node.superclass.initEvents.call(this);
        this.node.ui.iconNode.style.backgroundImage = 'url(' + this.node.attributes.bIcon + ')';
    },
    onDblClick : function(e){
        e.preventDefault();
        this.fireEvent("dblclick", this.node, e);
    }
});
