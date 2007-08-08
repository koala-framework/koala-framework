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

    this.menu = new Vps.Menu.Index('menuContainer', {role: this.role, pageId: config.pageId, controllerUrl: '/admin/menu/'});
    this.menu.on('menuevent', this.loadComponent, this, {componentName : 'cc'});

    this.editform = new Vps.Auto.Form.Dialog(null, {controllerUrl: '/admin/pageedit/', width: 400, height: 200});
    this.editform.on(
        'dataChanged',
        function(o, e) {
            values = this.editform.form.getValues();
            id = this.tree.tree.getSelectionModel().getSelectedNode().id;
            node = this.tree.tree.getNodeById(id).setText(values.name);
        },
        this
    );

    this.tree = new Vps.Auto.Tree('treeContainer', {controllerUrl: '/admin/pages/' });
    this.tree.toolbar2 = new Ext.Toolbar(Ext.get('treeContainer').createChild());
    this.tree.editButton = this.tree.toolbar2.addButton({
        disabled: true,
        text    : 'Bearbeiten',
        handler : 
            function (o, e) {
                node = this.tree.getSelectionModel().getSelectedNode();
                this.fireEvent('editcomponent', {id: node.attributes.id, cls: node.attributes.data.component_class, text: node.text});
            },
        icon : '/assets/vps/images/silkicons/page_edit.png',
        cls: "x-btn-text-icon",
        scope   : this.tree
    });
    this.tree.propertiesButton = this.tree.toolbar2.addButton({
        disabled: true,
        text    : 'Eigenschaften',
        handler :
            function (o, e) {
                this.editform.load(this.tree.tree.getSelectionModel().getSelectedNode().id);
                this.editform.show();
            },
        icon : '/assets/vps/images/silkicons/page_gear.png',
        cls: "x-btn-text-icon",
        scope   : this
    });

    this.tree.on('selectionchange', this.treeSelectionchange, this.tree);
    this.tree.on('editcomponent', this.loadComponent, this);

    this.created = new Array();
}

Ext.extend(Vps.Component.Pages, Ext.util.Observable,
{
    loadComponent: function (data, options)
    {
        if (data.controllerUrl != undefined) { // Falls von MenuEvent kommt
            controllerUrl = data.controllerUrl;
        } else {
            controllerUrl = '/component/edit/' + data.cls + '/' + data.id + '/';;
        }
        Ext.Ajax.request({
            url: controllerUrl + 'jsonIndex/',
            success: function(r) {
                response = Ext.decode(r.responseText);
                cls = eval(response['class']);
                if (cls) {
                    if (cls.prototype.getPanel) {
                        if (this.created[controllerUrl] != undefined && this.created[controllerUrl].getGrid() != undefined) {
                            panel = this.created[controllerUrl];
                            panel.refresh();
                        } else {
                            component = new cls(Vps.mainLayout.el.createChild(), Ext.applyIf(response.config, {controllerUrl: controllerUrl}));
                            component.text = data.text;
                            var panel = component.getPanel(data.text);
                            this.created[controllerUrl] = panel;
                            if (component.on) {
                                component.on('editcomponent', this.loadComponent, this, {componentName : data.text});
                            }
                        }
                    } else {
                        var name = controllerUrl;
                        var panel = new Ext.ContentPanel(name, {autoCreate:true, title: data.text, fitToFrame:true, closable:true, autoScroll: true, fitContainer: true});
                        Ext.DomHelper.overwrite(name, '');
                        component = new cls(name, Ext.applyIf(response.config, {controllerUrl: controllerUrl}));
                        component.text = data.text;
                        Vps.mainLayout.add('center', panel);
                        if (component.on) {
                            component.on('editcomponent', this.loadComponent, this, {componentName : data.text});
                        }
                    }
                    Vps.mainLayout.add('center', panel);
                }
            },
            scope: this
        });
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
