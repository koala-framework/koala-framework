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


    var menu = new Vps.Menu.Index('menuContainer', {role: this.role, pageId: config.pageId});
    var tree = new Vps.AutoTree('treeContainer', {controllerUrl: '/admin/pages/' });
    tree.toolbar2 = new Ext.Toolbar(Ext.get('treeContainer').createChild());
    tree.editButton = tree.toolbar2.addButton({
        disabled: true,
        text    : 'Bearbeiten',
        handler : 
            function (o, e) {
                node = this.tree.getSelectionModel().getSelectedNode();
                this.fireEvent('editcomponent', {id: node.id, text: node.text});
            },
        icon : '/assets/vps/images/silkicons/page_edit.png',
        cls: "x-btn-text-icon",
        scope   : tree
    });
    tree.propertiesButton = tree.toolbar2.addButton({
        disabled: true,
        text    : 'Eigenschaften',
        handler : this.treeEditProperties,
        icon : '/assets/vps/images/silkicons/page_gear.png',
        cls: "x-btn-text-icon",
        scope   : tree
    });

    tree.on('selectionchange', this.treeSelectionchange, this.tree);
    

    this.loadComponent = function (data) { 
        if (!data.id) { data.id = data.url; }
        if (!data.url) {
            data.url = '/component/' + data.id + '/';
        }
        data.url += 'jsonIndex/';
        Ext.Ajax.request({
            url: data.url,
            success: function(r) {
                layout.add('center', new Ext.ContentPanel('component' + data.id, {autoCreate:true, title: data.text, fitToFrame:true, closable:true, autoScroll: true}));
                response = Ext.decode(r.responseText);
                cls = eval(response['class']);
                if (cls) {
                    component = new cls('component' + data.id, response.config);
                    if (component.on) {
                        component.on('editcomponent', this.loadComponent, this);
                    }
                }
            },
            scope: this
        });
    }
    tree.on('editcomponent', this.loadComponent, this);
    menu.on('loadpage', this.loadComponent, this);
}

Ext.extend(Vps.Admin.Pages.Index, Ext.util.Observable,
{
    treeEditProperties : function (o, e) {
        var dlg = new Ext.BasicDialog('dialog', {
            height: 200,
            width: 350,
            minHeight: 100,
            minWidth: 150,
            modal: true,
            proxyDrag: true,
            shadow: true,
            autoCreate: true
        });
        dlg.addKeyListener(27, dlg.hide, dlg);

        form = new Ext.form.Form({
            labelWidth: 100,
            url: '/admin/pages/jsonSavePage',
            baseParams: {
                id: this.tree.getSelectionModel().getSelectedNode().id
            }
        });
    
        form.add(
            new Ext.form.TextField({
                allowBlank: false,
                blankText: 'Seitenname wird benötigt',
                fieldLabel: 'Seitenname',
                name: 'name',
                minLength: 1,
                maxLength: 255,
                width: 200,
                value: this.tree.getSelectionModel().getSelectedNode().attributes.text
            })
        );
        
        dlg.addButton(
            'Speichern',
            function (o, e) {
                form.submit({
                    success: function(form, a) {
                        node = this.tree.getNodeById(a.result.id);
                        node.setText(a.result.name);
                    },
                    scope: this
                })
                dlg.hide();
            },
            this
        );
            
        dlg.addButton('Abbrechen', dlg.hide, dlg);
        form.render(dlg.body);
        dlg.show();
    },
    
    treeSelectionchange : function (node) {
        if (node) {
            if (node.attributes.type == 'root') {
                this.visibleButton.enable();
                this.editButton.enable();
                this.propertiesButton.enable();
                this.addButton.disable();
                this.deleteButton.disable();
            } else if (node.attributes.type == 'category') {
                this.visibleButton.disable();
                this.propertiesButton.disable();
                this.editButton.disable();
                this.addButton.enable();
                this.deleteButton.disable();
            } else {
                this.visibleButton.enable();
                this.editButton.enable();
                this.propertiesButton.enable();
                this.addButton.enable();
                this.deleteButton.enable();
            }
        }
    }
}
)
