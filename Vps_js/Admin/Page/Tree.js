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
            if (node != undefined) {
                if (node.attributes.isParagraphs) {
                    this.addButton.enable();
                } else if (node.parentNode.attributes.isParagraphs) {
                    this.addButton.enable();
                } else {
                    this.addButton.disable();
                }
                this.fireEvent('selectionchange', node)
            }
        }, this
    );

    this.tree.render();
    this.tree.getRootNode().expand();

    var componentMenu = new Ext.menu.Menu({id: 'componentMenu'});
    for (var i in config.components) {
        componentMenu.addItem(
            new Ext.menu.Item({
                id: i,
                text: config.components[i],
                handler: this.onAdd,
                scope: this
            })
        );
    }

    // Toolbar
    this.toolbar = new Ext.Toolbar(Ext.DomHelper.insertFirst(renderTo, '<div \/>'));
    this.addButton = this.toolbar.addButton({
        disabled: true,
        text    : 'Absatz einfügen',
        menu: componentMenu
    });

}

Ext.extend(Vps.Admin.Page.Tree, Ext.util.Observable,
{
    onAdd : function(o, p) {
        new Vps.Connection().request({
            url: '/admin/page/ajaxCreateParagraph',
            method: 'post',
            scope: this,
            params: {
                pageId : this.pageId,
                componentClass : o.id,
                componentId : this.tree.getSelectionModel().getSelectedNode().id,
                parentComponentId : this.tree.getSelectionModel().getSelectedNode().parentNode.id
            },
            callback: function(options, bSuccess, response) {
                var o = Ext.decode(response.responseText);
                this.tree.getNodeById(o.parentComponentId).reload();
                this.tree.getSelectionModel().select(this.tree.getNodeById(o.parentComponentId));
            }
        });
    },

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
