Vps.Admin.Pages.Index = function(renderTo, config)
{
    var layout = new Ext.BorderLayout(renderTo, {
        north: {
            split: false, initialSize: 30
        },
        west: {
            split:true,
            initialSize: 400,
            titlebar: true,
            collapsible: true,
            minSize: 200,
            maxSize: 600
        },
        center: {
            split:true,
            initialSize: 200,
            minSize: 100,
            maxSize: 400,
            autoScroll:true,
            tabPosition: 'top',
            closeOnTab: true
        }
    });
    
    layout.beginUpdate();
    layout.add('north', new Ext.ContentPanel('menuContainer', {autoCreate: true, fitToFrame:true}));
    layout.add('west', new Ext.ContentPanel('treeContainer', {autoCreate:true, title: 'Seitenbaum', fitToFrame:true}));
    layout.restoreState();
    layout.endUpdate();

    new Vps.Menu.Index('menuContainer', {role: this.role, pageId: config.pageId});
    this.tree = new Vps.Admin.Pages.Tree('treeContainer', {panel: 'center' });
    
    this.tree.on('editPage', function (node) { 
        if (node) {
            Ext.Ajax.request({
                url: '/component/' + node.id + '/jsonIndex',
                success: function(r) {
                    response = Ext.decode(r.responseText);
                    class = eval(response.class);
                    layout.add('center', new Ext.ContentPanel('page' + node.id, {autoCreate:true, title: node.text, fitToFrame:true}));
                    new class('page' + node.id, response.config);
                },
                scope: this
            });
        }
    }, this);

}
