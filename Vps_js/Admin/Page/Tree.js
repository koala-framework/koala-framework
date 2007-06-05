Vps.Admin.Page.Tree = function(renderTo, config)
{
    Ext.apply(this, config);
    this.events = {
        'selectionchange' : true
    };

    // Baum
    this.tree = new Ext.tree.TreePanel(renderTo, {
        animate:true,
        loader: new Ext.tree.TreeLoader({dataUrl:'/admin/page/ajaxGetNodes', baseParams: { pageId:config.pageId }}),
        containerScroll: true,
        rootVisible: false
    });

    this.tree.setRootNode(
        new Ext.tree.AsyncTreeNode({
            text: '',
            draggable:false,
            id: 'root'
        })
    );

    this.tree.getSelectionModel().on('selectionchange',
        function (e, node) {
            this.fireEvent('selectionchange', node)
        }, this
    );

    this.tree.render();
    this.tree.getRootNode().expand();
}

Ext.extend(Vps.Admin.Page.Tree, Ext.util.Observable,
{
});

MyNodeUI = function(node){
    MyNodeUI.superclass.constructor.call(this, node);
}
Ext.extend(MyNodeUI, Ext.tree.TreeNodeUI, {
    initEvents : function(){
        MyNodeUI.superclass.initEvents.call(this);
        if(this.node.attributes.status == '0'){
            this.addClass('offline');
        }
    }
});
