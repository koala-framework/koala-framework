Ext.namespace('Vps.Component');

Vps.Component.Pages = function(renderTo, config)
{
    this.renderTo = renderTo;
    this.layout = new Ext.BorderLayout(this.renderTo, {
        west: {
            split:true,
            initialSize: 400,
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
    
    this.layout.beginUpdate();
    this.layout.add('west', new Ext.ContentPanel('treeContainer', {autoCreate:true, title: 'Seitenbaum', fitToFrame:true}));
    this.layout.restoreState();
    this.layout.endUpdate();
    
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
    
    this.createButtons();    
    var toolbar = new Ext.Toolbar(Ext.get('treeContainer').createChild());
    this.tree.editButton = new Ext.Toolbar.Button(this.buttons.edit);
    this.tree.pageButton = new Ext.Toolbar.Button({
        cls: 'x-btn-text-icon bmenu',
        text:'Page',
        menu: [this.buttons.properties, this.buttons.add, this.buttons.del, this.buttons.visible],
        icon : '/assets/vps/images/silkicons/page.png',
        disabled: true
    });
    this.tree.navigationButton = new Ext.Toolbar.Button({
        cls: 'x-btn-text-icon bmenu',
        text:'Navigation',
        icon : '/assets/vps/images/silkicons/weather_sun.png',
        menu: [this.buttons.reloadAll, this.buttons.expandAll, this.buttons.collapseAll]
    });
    toolbar.add(
        this.tree.editButton, '-',
        this.tree.pageButton, '-',
        this.tree.navigationButton
    );

    this.tree.on('selectionchange', this.treeSelectionchange, this.tree);
    this.tree.on('editcomponent', this.loadComponent, this);
    this.tree.on('loaded', function(o, e) {
        this.tree.tree.on('contextmenu', function (node) {
            node.select();
            var menu = new Ext.menu.Menu({
                 items: [this.buttons.edit, '-', this.buttons.properties, this.buttons.add, this.buttons.del, this.buttons.visible, '-', this.buttons.reloadAll, this.buttons.expand, this.buttons.collapse]   
            });
            menu.show(node.ui.getAnchor());
        }, this);

        this.tree.tree.on('dblclick', function (o, e) {
            this.fireEvent('editcomponent', {id: o.attributes.id, cls: o.attributes.data.component_class, text: o.text})
        }, this.tree);
    }, this)

    this.created = new Array();
}

Ext.extend(Vps.Component.Pages, Ext.util.Observable,
{
    createButtons : function()
    {
        this.buttons = {};
        this.buttons.edit = {
            text: 'Edit Content',
            handler : function (o, e) {
                node = this.tree.getSelectionModel().getSelectedNode();
                this.fireEvent('editcomponent', {id: node.attributes.id, cls: node.attributes.data.component_class, text: node.text});
            },
            icon : '/assets/vps/images/silkicons/page_edit.png',
            cls: 'x-btn-text-icon',
            scope   : this.tree
        };
        this.buttons.properties = {
            text    : 'Properties of selected Page',
            handler : function (o, e) {
                this.editform.load(this.tree.tree.getSelectionModel().getSelectedNode().id);
                this.editform.show();
            },
            icon : '/assets/vps/images/silkicons/page_gear.png',
            cls: 'x-btn-text-icon',
            scope   : this
        }
        this.buttons.add = {
            text    : 'Add new Subpage',
            handler : this.tree.add,
            icon : '/assets/vps/images/silkicons/page_add.png',
            cls: 'x-btn-text-icon',
            scope   : this.tree
        }
        this.buttons.del = {
            text    : 'Delete selected Page',
            handler : this.tree.del,
            icon : '/assets/vps/images/silkicons/page_delete.png',
            cls: 'x-btn-text-icon',
            scope   : this.tree
        }
        this.buttons.visible = {
            text    : 'Toggle Visibility of selected Page',
            handler : this.tree.visible,
            icon : '/assets/vps/images/silkicons/page_red.png',
            cls: 'x-btn-text-icon',
            scope   : this.tree
        }
        this.buttons.reloadAll = {
            text    : 'Reload all',
            handler : function () { this.tree.getRootNode().reload(); },
            icon : '/assets/vps/images/silkicons/bullet_star.png',
            cls: 'x-btn-text-icon',
            scope   : this.tree
        };
        this.buttons.expand = {
            text    : 'Expand here',
            handler : function () { this.tree.getSelectionModel().getSelectedNode().expand(true); },
            icon : '/assets/vps/images/silkicons/bullet_add.png',
            cls: 'x-btn-text-icon',
            scope   : this.tree
        };
        this.buttons.collapse = {
            text    : 'Collapse here',
            handler : function () { this.tree.getSelectionModel().getSelectedNode().collapse(true); },
            icon : '/assets/vps/images/silkicons/bullet_delete.png',
            cls: 'x-btn-text-icon',
            scope   : this.tree
        };
        this.buttons.expandAll = {
            text    : 'Expand All',
            handler : function () { this.tree.expandAll(); },
            icon : '/assets/vps/images/silkicons/bullet_add.png',
            cls: 'x-btn-text-icon',
            scope   : this.tree
        };
        this.buttons.collapseAll = {
            text    : 'Collapse all',
            handler : function () { this.tree.collapseAll(); },
            icon : '/assets/vps/images/silkicons/bullet_delete.png',
            cls: 'x-btn-text-icon',
            scope   : this.tree
        };
    },
    getPanel : function()
    {
        return new Ext.NestedLayoutPanel(this.layout);
    },
    
    createLayoutInstance: function(id)
    {
        if (this.created[id] == undefined) {
            var layout = new Ext.BorderLayout(Ext.get(this.renderTo).createChild(), {
                north: { initialSize: 30 },
                center: { }
            });
            var el = layout.el.createChild();
            var toolbar = new Ext.Toolbar(el);
            layout.add('north', new Ext.ContentPanel(el, {autoCreate: true, toolbar: toolbar}));
            layout.loadComponent = function(data)
            {
                Ext.Ajax.request({
                    url: data.controllerUrl + 'jsonIndex/',
                    success: function(r) {
                        response = Ext.decode(r.responseText);
                        cls = eval(response['class']);
                        if (cls) {
                            this.addToolbarButton(data);
                            this.getRegion('center').remove(0, false);
                            if (cls.prototype.getPanel) {
                                component = new cls(this.el.createChild(), Ext.applyIf(response.config, {controllerUrl: data.controllerUrl}));
                                var panel = component.getPanel(data.text);
                            } else {
                                var panel = new Ext.ContentPanel(data.controllerUrl, {autoCreate:true, autoScroll: true, fitToFrame: true});
                                component = new cls(data.controllerUrl, Ext.applyIf(response.config, {controllerUrl: data.controllerUrl}));
                            }
                    
                            if (component.events != undefined && component.on) {
                                component.on('editcomponent', this.loadComponent, this);
                            }
                            this.add('center', panel);
                        }
                    },
                    scope: this
                });
            }
            layout.addToolbarButton = function(data)
            {
                toolbar = this.getRegion('north').getPanel(0).getToolbar();
                var count = toolbar.items.getCount();
                del = count;
                for (var x=0; x<count; x++){
                    var item = toolbar.items.itemAt(x);
                    if (item.params != undefined && item.params.controllerUrl == data.controllerUrl) {
                        del = x > 0 ? x - 1 : x;
                        x = count;
                    }
                }
                for (var x=count-1; x>=del; x--){
                    var item = toolbar.items.itemAt(x);
                    toolbar.items.removeAt(x);
                    item.destroy();
                }
                if (toolbar.items.getCount() >= 1) {
                     toolbar.addSeparator();
                }
                toolbar.addButton({
                    text    : data.text,
                    handler : function (o, e) {
                        this.loadComponent(data);
                    },
                    params: data,
                    scope   : this
                });
            }

            var panel = new Ext.NestedLayoutPanel(layout, {autoCreate: true, title: id, fitToFrame:true, closable:true, autoScroll: true});
            panel.setTitle(id);
            this.layout.add('center', panel);
            this.layout.getRegion('center').on('panelremoved', function(o, e) { this.created[e.getTitle()] = undefined; }, this);
            this.created[id] = layout;
        } else {
            this.layout.getRegion('center').showPanel(this.created[id]);
        }
        return this.created[id];
    },
    
    loadComponent: function (data)
    {
        data.controllerUrl = '/component/edit/' + data.cls + '/' + data.id + '/';;
        var layout = this.createLayoutInstance(data.text);
        layout.loadComponent(data);
    },
    
    treeSelectionchange : function (node) {
        if (node) {
            if (node.attributes.type == 'root') {
            } else if (node.attributes.type == 'category') {
                debugger;
                this.pageButton.enable();
            } else {
                this.pageButton.enable();
            }
        }
    }
}
)

Ext.namespace('Vps.AutoTree');
Vps.AutoTree.PagesNode = function(node){
    Vps.AutoTree.PagesNode.superclass.constructor.call(this, node);
}

Ext.extend(Vps.AutoTree.PagesNode, Vps.AutoTree.Node, {
    onDblClick : function(e){
        e.preventDefault();
        this.fireEvent("dblclick", this.node, e);
    }
});
