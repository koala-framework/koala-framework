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
            tabPosition: 'top',
            autoScroll: false
        }
    });
    
    layout.beginUpdate();
    layout.add('north', new Ext.ContentPanel('menuContainer', {autoCreate: true, fitToFrame:true}));
    layout.add('west', new Ext.ContentPanel('treeContainer', {autoCreate:true, title: 'Seitenbaum', fitToFrame:true}));
    layout.add('center', new Ext.ContentPanel('page', {autoCreate:true, title: 'Seiteninhalt', fitToFrame:true}));
    layout.add('center', new Ext.ContentPanel('pageProperties', {autoCreate:true, title: 'Seiteneigenschaften', fitToFrame:true}));
    layout.showPanel('page');
    layout.restoreState();
    layout.endUpdate();

    new Vps.Menu.Index('menuContainer', {role: this.role, pageId: config.pageId});
    this.tree = new Vps.Admin.Pages.Tree('treeContainer', config);
    
    this.tree.on('selectionchange', function (node) { 
        if (node) {
            Ext.DomHelper.overwrite('page', '');
            Ext.Ajax.request({
                url: '/component/' + node.id + '/jsonIndex',
                success: function(r) {
                    response = Ext.decode(r.responseText);
                    class = eval(response.class);
                    new class('page', response.config);
                },
                scope: this
            });
        }
    }, this);

}
