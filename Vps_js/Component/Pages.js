Ext.namespace('Vps.Component');
Vps.Component.Pages = function(renderTo, config)
{
    Vps.mainLayout = new Ext.BorderLayout(renderTo, {
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
            alwaysShowTabs : true,
            autoScroll: true
        }
    });
    
    Vps.mainLayout.beginUpdate();
    Vps.mainLayout.add('north', new Ext.ContentPanel('menuContainer', {autoCreate: true, fitToFrame:true}));
    Vps.mainLayout.add('west', new Ext.ContentPanel('treeContainer', {autoCreate:true, title: 'Seitenbaum', fitToFrame:true}));
    Vps.mainLayout.restoreState();
    Vps.mainLayout.endUpdate();

    var menu = new Vps.Menu.Index('menuContainer', {role: this.role, pageId: config.pageId, controllerUrl: '/admin/menu/'});
    var tree = new Vps.Auto.Tree('treeContainer', {controllerUrl: '/admin/pages/' });
    tree.toolbar2 = new Ext.Toolbar(Ext.get('treeContainer').createChild());
    tree.editButton = tree.toolbar2.addButton({
        disabled: true,
        text    : 'Bearbeiten',
        handler : 
            function (o, e) {
                node = this.tree.getSelectionModel().getSelectedNode();
                this.fireEvent('editcomponent', {id: node.attributes.id, cls: node.attributes.data.component_class, text: node.text});
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

    tree.on('editcomponent', this.loadComponent, this);
    menu.on('menuevent', this.loadComponent, this);
}

Ext.extend(Vps.Component.Pages, Ext.util.Observable,
{
    loadComponent: function (data)
    {
        if (data.url != undefined) { // Falls von MenuEvent kommt
            controllerUrl = data.url;
            data.text = data.title;
        } else {
            controllerUrl = '/component/edit/' + data.cls + '/' + data.id + '/';
        }
        Ext.Ajax.request({
            url: controllerUrl + 'jsonIndex/',
            success: function(r) {
                var name = controllerUrl;
                Vps.mainLayout.add('center', new Ext.ContentPanel(name, {autoCreate:true, title: data.text, fitToFrame:true, closable:true, autoScroll: true, fitContainer: true}));
                Ext.DomHelper.overwrite(name, '');
                response = Ext.decode(r.responseText);
                cls = eval(response['class']);
                if (cls) {
                    component = new cls(name, Ext.applyIf(response.config, {controllerUrl: controllerUrl}));
                    if (component.on) {
                        component.on('editcomponent', this.loadComponent, this);
                    }
                }
            },
            scope: this
        });
    },
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
