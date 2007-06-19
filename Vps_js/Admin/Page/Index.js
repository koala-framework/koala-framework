Vps.Admin.Page.Index = function(renderTo, config) {
    
    var layout = new Ext.BorderLayout(renderTo, {
        north: {
            split: false, initialSize: 30
        },
        west: {
            split:true,
            initialSize: 300,
            minSize: 200,
            titlebar: true,
            useShim: true,
            collapsible: true,
            maxSize: 600
        },
        center: {
            autoScroll: false,
            titlebar: true
        }
    });

    var innerLayout = new Ext.BorderLayout(Ext.get(renderTo).createChild(), {
        south: {
            split:true,
            initialSize: 200,
            minSize: 100,
            maxSize: 400,
            autoScroll:true,
            titlebar: true
        },
        center: {
            autoScroll:true
        }
    });

    layout.beginUpdate();
    
    innerLayout.add('center', new Ext.ContentPanel('treeContainer', {autoCreate:true}));
    innerLayout.add('south', new Ext.ContentPanel('formContainer', {autoCreate:true, title: 'Eigenschaften des gewählten Seitenbausteins', fitToFrame:true}));
    
    layout.add('north', new Ext.ContentPanel('menuContainer', {autoCreate: true, fitToFrame:true}));
    layout.add('west', new Ext.NestedLayoutPanel(innerLayout, {autoCreate:true, title: 'Aufbau der aktuellen Seite', fitToFrame:true}));
    layout.add('center', new Ext.ContentPanel('component', {autoCreate: true, title: 'Inhalt des gewählten Seitenbausteins', fitToFrame:true}));
    layout.restoreState();
    layout.endUpdate();

    new Vps.Menu.Index('menuContainer', {role: this.role, pageId: config.pageId});
    this.tree = new Vps.Admin.Page.Tree('treeContainer', config);
    var form = new Vps.Admin.Page.Form('formContainer', config);

    this.tree.on('selectionchange', function (node) { 
        if (node) {
            Ext.DomHelper.overwrite('component', '');
            Ext.get(document.body).mask('Komponente wird geladen...');
            Ext.Ajax.request({
                url: '/component/' + node.id + '/jsonIndex',
                success: function(r) {
                    Ext.get(document.body).unmask();
                    response = Ext.decode(r.responseText);
                    class = eval(response.class);
                    new class('component', response.config);
                },
                scope: this
            });
        }
    });
    form.on('saved', function (result) {
        node = this.tree.tree.getSelectionModel().select(this.tree.tree.getNodeById(result.componentId));
        node.parentNode.select();
        node.parentNode.reload();
    });
    //var toolbar = new Toolbar(west1, config.pageId);
}