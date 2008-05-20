Vps.Component.Pages = Ext.extend(Ext.Panel, {
    initComponent : function()
    {
        this.treePanel = new Vps.Auto.SyncTreePanel({
            controllerUrl: '/admin/component/pages',
            title       : 'Seitenbaum',
            region      : 'west',
            split       : true,
            width       : 300,
            collapsible : true,
            minSize     : 200,
            maxSize     : 600,
            tbar        : []
        });
        this.contentTabPanel = new Ext.TabPanel({
            region      : 'center'
        });

        this.layout = 'border';
        this.actions = {};
        this.items = [this.treePanel, this.contentTabPanel];
        Vps.Component.Pages.superclass.initComponent.call(this);

        this.treePanel.on('loaded', this.onTreePanelLoaded, this);
        this.on('editcomponent', this.loadComponent, this);
        this.setupEditform();
    },

    setupEditform : function ()
    {
        this.editDialog = new Vps.Auto.Form.Window({
            width: 400,
            height: 400,
            controllerUrl: '/admin/component/pageEdit'
        });
        this.editDialog.on('datachange', function(test) {
            this.treePanel.tree.root.reload();
        }, this);
    },

    onTreePanelLoaded : function(tree)
    {
        this.pageButton = new Ext.Toolbar.Button({
            cls     : 'x-btn-text-icon bmenu',
            text    : trlVps('Page'),
            menu    : [
                this.getAction('properties'),
                this.getAction('add'),
                this.getAction('delete'),
                this.getAction('visible'),
                this.getAction('makeHome'),
                this.getAction('preview')
            ],
            icon    : '/assets/silkicons/page.png',
            disabled: true
        });

        this.treePanel.getTopToolbar().add(
            this.pageButton,
            '-',
            this.getAction('edit'),
            '->',
            this.getAction('reloadAll')
        );

        this.contextMenu = new Ext.menu.Menu({
             items: [
                this.getAction('edit'),
                '-',
                this.getAction('properties'),
                this.getAction('add'),
                this.getAction('delete'),
                this.getAction('visible'),
                this.getAction('makeHome'),
                this.getAction('preview'),
                '-',
                this.getAction('expand'),
                this.getAction('collapse')
            ]
        });

        tree.on('click', this.treeSelectionchange, this);
        tree.on('contextmenu', function (node) {
            node.select();
            this.treeSelectionchange(node);
            this.contextMenu.show(node.ui.getAnchor());
        }, this);

        tree.on('dblclick', function (o, e) {
            this.fireEvent('editcomponent', {id: o.attributes.id, cls: o.attributes.data.component_class, text: o.text})
        }, this);
    },

    treeSelectionchange : function (node) {
        if (node) {
            this.pageButton.enable();
            if (node.attributes.type == 'category') {
                this.getAction('edit').disable();
                this.getAction('properties').disable();
                this.getAction('delete').disable();
                this.getAction('visible').disable();
                this.getAction('makeHome').disable();
            } else {
                this.getAction('edit').enable();
                this.getAction('properties').enable();
                this.getAction('delete').enable();
                this.getAction('visible').enable();
                this.getAction('makeHome').enable();
            }
        }
    },

    loadComponent: function(data)
    {
        var panel = this.contentTabPanel.getItem('page'+data.id);
        if (!panel) {
            panel = this.createComponentPanel(data);
            this.contentTabPanel.add(panel);
        }
        this.contentTabPanel.setActiveTab(panel);

        panel.applyBaseParams({
            id: data.id
        });
        panel.load({
            componentClass: data.cls
        });
    },

    createComponentPanel: function(data)
    {
        var panel = new Vps.Component.ComponentPanel({
            id          : 'page'+data.id,
            title       : data.text,
            region      : 'center',
            closable    : true,
            autoLoad    : false
        });
        return panel;
    },

    getAction : function(type)
    {
        if (this.actions[type]) return this.actions[type];

        if (type == 'edit') {
            this.actions[type] = new Ext.Action({
                text    : trlVps('Edit Content'),
                disabled: true,
                handler : function (o, e) {
                    node = this.treePanel.tree.getSelectionModel().getSelectedNode();
                    this.fireEvent('editcomponent', {id: node.attributes.id, cls: node.attributes.data.component_class, text: node.text});
                },
                icon    : '/assets/silkicons/page_edit.png',
                cls     : 'x-btn-text-icon',
                scope   : this
            });
        } else if (type == 'properties') {
            this.actions[type] = new Ext.Action({
                text    : trlVps('Properties of selected Page'),
                handler : function () {
                    this.editDialog.getAutoForm().setBaseParams({});
                    this.editDialog.showEdit(this.treePanel.tree.selModel.selNode.id);
                },
                icon    : '/assets/silkicons/page_gear.png',
                cls     : 'x-btn-text-icon',
                scope   : this
            });
        } else if (type == 'add') {
            this.actions[type] = new Ext.Action({
                text    : trlVps('Add new Subpage'),
                handler : function () {
                    this.editDialog.getAutoForm().setBaseParams({
                        parent_id: this.treePanel.tree.selModel.selNode.id
                    });
                    this.editDialog.showAdd();
                },
                icon    : '/assets/silkicons/page_add.png',
                cls     : 'x-btn-text-icon',
                scope   : this
            });
        } else if (type == 'delete') {
            this.actions[type] = new Ext.Action({
                text    : trlVps('Delete selected Page'),
                handler : this.treePanel.onDelete,
                icon    : '/assets/silkicons/page_delete.png',
                cls     : 'x-btn-text-icon',
                scope   : this.treePanel
            });
        } else if (type == 'visible') {
            this.actions[type] = new Ext.Action({
                text    : trlVps('Toggle Visibility of selected Page'),
                handler : this.treePanel.onVisible,
                icon    : '/assets/silkicons/page_red.png',
                cls     : 'x-btn-text-icon',
                scope   : this.treePanel
            });
        } else if (type == 'makeHome') {
            this.actions[type] = new Ext.Action({
                text    : trlVps('Make selected Page Homepage'),
                handler : function (o, e) {
                    Ext.Ajax.request({
                        url: '/admin/component/pages/json-make-home',
                        success: function(r) {
                            response = Ext.decode(r.responseText);
                            var oldhome = this.treePanel.tree.getNodeById(response.oldhome);
                            oldhome.attributes.visible = response.oldhomeVisible;
                            this.treePanel.setVisible(oldhome);
                            var home = this.treePanel.tree.getNodeById(response.home);
                            home.ui.iconNode.style.backgroundImage = 'url(/assets/silkicons/application_home.png)';
                        },
                        params: {id: this.treePanel.tree.getSelectionModel().getSelectedNode().id },
                        scope: this
                    });
                },
                icon    : '/assets/silkicons/application_home.png',
                cls     : 'x-btn-text-icon',
                scope   : this
            });
        } else if (type == 'reloadAll') {
            this.actions[type] = new Ext.Action({
                handler : function () { this.treePanel.tree.getRootNode().reload(); },
                icon    : '/assets/silkicons/arrow_rotate_clockwise.png',
                cls     : 'x-btn-icon',
                tooltip : 'Reload',
                scope   : this
            });
        } else if (type == 'expand') {
            this.actions[type] = new Ext.Action({
                text    : trlVps('Expand here'),
                handler : function () { this.treePanel.tree.getSelectionModel().getSelectedNode().expand(true); },
                icon    : '/assets/silkicons/bullet_add.png',
                cls     : 'x-btn-text-icon',
                scope   : this
            });
        } else if (type == 'collapse') {
            this.actions[type] = new Ext.Action({
                text    : trlVps('Collapse here'),
                handler : function () { this.treePanel.tree.getSelectionModel().getSelectedNode().collapse(true); },
                icon    : '/assets/silkicons/bullet_delete.png',
                cls     : 'x-btn-text-icon',
                scope   : this
            });
        } else if (type == 'preview') {
            this.actions[type] = new Ext.Action({
                text    : trlVps('Open Preview'),
                handler : function () {
                    window.open('/admin/component/pages/openPreview?page_id='+
                                this.treePanel.getSelectedId());
                },
                icon    : '/assets/silkicons/page_white_magnify.png',
                cls     : 'x-btn-text-icon',
                scope   : this
            });
        } else {
            throw 'unknown action-type: ' + type;
        }
        return this.actions[type];
    }

});

Vps.Component.PagesNode = Ext.extend(Vps.Auto.TreeNode, {
    onDblClick : function(e){
        e.preventDefault();
        this.fireEvent("dblclick", this.node, e);
    }
});
