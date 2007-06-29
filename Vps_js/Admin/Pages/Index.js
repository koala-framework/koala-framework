Ext.namespace('Vps.Admin.Pages');
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
            closeOnTab: true,
            alwaysShowTabs : true
        }
    });
    
    layout.beginUpdate();
    layout.add('north', new Ext.ContentPanel('menuContainer', {autoCreate: true, fitToFrame:true}));
    layout.add('west', new Ext.ContentPanel('treeContainer', {autoCreate:true, title: 'Seitenbaum', fitToFrame:true}));
    layout.restoreState();
    layout.endUpdate();

    new Vps.Menu.Index('menuContainer', {role: this.role, pageId: config.pageId});
    this.tree = new Vps.Admin.Pages.Tree('treeContainer', {panel: 'center' });
    
    
    this.loadComponent = function (data) { 
        Ext.Ajax.request({
            url: '/component/' + data.id + '/jsonIndex',
            success: function(r) {
                layout.add('center', new Ext.ContentPanel('component' + data.id, {autoCreate:true, title: data.text, fitToFrame:true, closable:true}));
                response = Ext.decode(r.responseText);
                class = eval(response.class);
                if (class) {
                    component = new class('component' + data.id, response.config);
                    if (component.on) {
                        component.on('editcomponent', this.loadComponent, this);
                    }
                }
            },
            scope: this
        });
    }
    this.tree.on('editcomponent', this.loadComponent, this);
}
