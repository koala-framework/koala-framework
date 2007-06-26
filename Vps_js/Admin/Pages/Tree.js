Vps.Admin.Pages.Tree = function(renderTo, config)
{
    Ext.apply(this, config);
    this.events = {
        'selectionchange' : true
    };

    this.tree = new Ext.tree.TreePanel(renderTo, {
        animate:true, 
        loader: new Ext.tree.TreeLoader({dataUrl:'/admin/pages/ajaxGetNodes'}),
        enableDD:true,
        containerScroll: true,
        rootVisible: false
    });

    this.tree.setRootNode(
        new Ext.tree.AsyncTreeNode({
            text: '',
            draggable:false,
            id:'root'
        })
    );
/*
    this.tree.on('collapse',
        function(e){
            var conn = new Ext.Ajax.request({
                url: '/admin/pages/ajaxCollapseNode',
                params: {id: e.id},
                method: 'post'
            });
        }
    );
    
    this.tree.on('nodedrop',
        function(e){
            var callback = function(option, success, response)
            {
                eval('result = ' + response.responseText);
                if (!result.success) {
                    // Falls Fehler aufgetreten ist, einfach neu laden
                    document.location.href = '/admin/pages';
                }
            }
            var params = {
                command: 'move',
                source: e.dropNode.id,
                target: e.target.id,
                point: e.point
            }
            var config = {
                url: '/admin/pages/ajaxProcessPageData',
                params: params,
                method: 'post',
                callback: callback,
                scope: this
            }

            var conn = new Ext.data.Connection();
            conn.request(config);
        }
    );
   */ 
    this.tree.getSelectionModel().on('selectionchange',
        function (e, node) {
            this.fireEvent('selectionchange', node)
        }, this
    );
    
    this.tree.render();
    this.tree.getRootNode().expand();
}

Ext.extend(Vps.Admin.Pages.Tree, Ext.util.Observable,
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
