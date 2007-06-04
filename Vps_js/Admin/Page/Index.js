Vps.Admin.Page.Index = function(renderTo, config) {
    
    // Form
    // Konstruktor
    //west = Ext.DomHelper.append(document.body, '<div />', true);
    //west1 = Ext.DomHelper.append(west, '<div />', true);
    //west2 = Ext.DomHelper.append(west, '<div />', true);
    center = Ext.DomHelper.append(document.body, '<iframe id="main" frameborder="no" />', true);
    
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
    layout.add('center', new Ext.ContentPanel(center, {title: 'Inhalt des gewählten Seitenbausteins', fitToFrame:true}));
    layout.restoreState();
    layout.endUpdate();
    
    new Vps.Menu.Index('menuContainer', {role: this.role});
    var tree = new Vps.Admin.Page.Tree('treeContainer', config);
    var form = new Vps.Admin.Page.Form('formContainer', config);

    tree.on('selectionchange', function (node) { 
        if (node) {
            Ext.get('main').dom.src = '/admin/component?id=' + node.id;
            form.setup(node.id, node.attributes.selectedDecorators);
        }
    });
    form.on('saved', function (result) {
        node = tree.tree.getSelectionModel().select(tree.tree.getNodeById(result.componentId));
        node.parentNode.select();
        node.parentNode.reload();
    });
    //var toolbar = new Toolbar(west1, config.pageId);
}