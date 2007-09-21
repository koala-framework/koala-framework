Ext.namespace('Vps.AutoTree');
Vps.AutoTree.Node = Ext.extend(Ext.tree.TreeNodeUI, {
    initEvents : function(){
        Vps.AutoTree.Node.superclass.initEvents.call(this);
        this.node.ui.iconNode.style.backgroundImage = 'url(/assets/vps/images/silkicons/' + this.node.attributes.bIcon + '.png)';
    }
});

Vps.Auto.TreePanel = Ext.extend(Ext.Panel, {
    initComponent : function()
    {
	    this.addEvents({
	        selectionchange: true,
	        edit: true,
	        generatetoolbar: true,
	        generatetoolbarstart: true,
	        loaded: true
	    });

	    Ext.Ajax.request({
	        url: this.controllerUrl + 'jsonMeta',
	        params: this.baseParams,
	        success: this.init,
	        scope: this
	    });
        this.actions = {};
        this.autoScroll = true;

        Vps.Auto.TreePanel.superclass.initComponent.call(this);
    },

    getAction : function(type)
    {
        if (this.actions[type]) return this.actions[type];

        if (type == 'delete') {
            this.actions[type] = new Ext.Action({
                text    : 'Delete',
                handler : this.onDelete,
                icon    : '/assets/vps/images/silkicons/' + this.icons['delete'] + '.png',
                cls     : 'x-btn-text-icon',
                disabled: true,
                scope   : this
            });
        } else if (type == 'add') {
            this.actions[type] = new Ext.Action({
                text    : 'Add',
                handler : this.onAdd,
                icon    : '/assets/vps/images/silkicons/' + this.icons['add'] + '.png',
                cls     : 'x-btn-text-icon',
                scope   : this
            });
        } else if (type == 'edit') {
            this.actions[type] = new Ext.Action({
                text    : 'Edit',
                handler : this.onEdit,
                icon    : '/assets/vps/images/silkicons/' + this.icons['edit'] + '.png',
                cls     : 'x-btn-text-icon',
                disabled: true,
                scope   : this
            });
        } else if (type == 'invisible') {
            this.actions[type] = new Ext.Action({
                text    : 'Toggle Visibility',
                handler : this.onVisible,
                icon    : '/assets/vps/images/silkicons/' + this.icons['invisible'] + '.png',
                cls     : 'x-btn-text-icon',
                disabled: true,
                scope   : this
            });
        } else if (type == 'reload') {
            this.actions[type] = new Ext.Action({
                text    : '',
                handler : function () { this.tree.getRootNode().reload(); },
                icon    : '/assets/vps/images/silkicons/bullet_star.png',
                cls     : 'x-btn-icon',
                scope   : this
            });
        } else if (type == 'expand') {
            this.actions[type] = new Ext.Action({
                text    : '',
                handler : function () { this.tree.expandAll(); },
                icon    : '/assets/vps/images/silkicons/bullet_add.png',
                cls     : 'x-btn-icon',
                scope   : this
            });
        } else if (type == 'collapse') {
            this.actions[type] = new Ext.Action({
                text    : '',
                handler : function () { this.tree.collapseAll(); },
                icon    : '/assets/vps/images/silkicons/bullet_delete.png',
                cls     : 'x-btn-icon',
                scope   : this
            });
        } else {
            throw 'unknown action-type: ' + type;
        }
        return this.actions[type];
    },

    init: function(response) {
        r = Ext.decode(response.responseText);
        this.icons = r.icons;

        // Toolbar
        if (r.buttons.each == undefined) { // Abfrage nötig, falls keine Buttons geliefert
            var tbar = [];
            for (var button in r.buttons) {
                tbar.add(this.getAction(button));
            }
        }

        // Tree
        this.tree = new Ext.tree.TreePanel({
            border      : false,
            animate     : true,
            loader      : new Ext.tree.TreeLoader({dataUrl: this.controllerUrl + 'jsonData'}),
            enableDD    : r.enableDD,
            containerScroll: true,
            rootVisible : r.rootVisible,
            tbar        : tbar
        });

        this.tree.setRootNode(
            new Ext.tree.AsyncTreeNode({
                text: r.rootText,
                id: '0',
                allowDrag: false
            })
        );

        this.tree.getSelectionModel().on('selectionchange', this.onSelectionchange, this);
        this.tree.on('beforenodedrop', this.onMove, this);
        this.tree.on('collapsenode', this.onCollapse, this);
        this.tree.on('expandnode', this.onExpand, this);

        this.add(this.tree);
        this.doLayout();

        if (r.rootVisible) {
            this.tree.getRootNode().ui.iconNode.style.backgroundImage = 'url(/assets/vps/images/silkicons/' + r.icons.root + '.png)';
            //this.tree.getRootNode().select();
        }
        this.tree.getRootNode().expand();
        this.fireEvent('loaded', this.tree);
    },

    onSelectionchange: function (e, node) {
        if (node && node.id != 0) {
            this.getAction('edit').enable();
            this.getAction('invisible').enable();
            this.getAction('delete').enable();
        } else {
            this.getAction('edit').disable();
            this.getAction('invisible').disable();
            this.getAction('delete').disable();
        }
        this.fireEvent('selectionchange', node);
    },

    onAdd: function (o, e) {
        Ext.MessageBox.prompt('Hinzufügen', 'Geben Sie einen Namen ein.',
            function  (button, name) {
                if (button == 'ok') {
                    Ext.Ajax.request({
                        url: this.controllerUrl + 'jsonAdd',
                        params: {
                            parentId: this.tree.getSelectionModel().getSelectedNode().id,
                            name: name
                        },
                        success: function(r) {
                            response = Ext.decode(r.responseText);
                            this.addNode(response);
                        },
                        scope: this
                    })
                }
            },
            this
        );
    },

    addNode: function(response)
    {
        parentNode = this.tree.getNodeById(response.parentId);
        if (parentNode.isExpanded()) {
            response.config.uiProvider = eval(response.config.uiProvider);
            node = new Ext.tree.AsyncTreeNode(response.config);
            if (parentNode.firstChild) {
                this.tree.getSelectionModel().getSelectedNode().insertBefore(node, parentNode.firstChild);
            } else {
                this.tree.getSelectionModel().getSelectedNode().appendChild(node);
            }
            this.tree.getSelectionModel().select(this.tree.getNodeById(response.config.id));
        } else {
            parentNode.expand();
        }
    },

    onDelete: function (o, e) {
        Ext.MessageBox.confirm('Löschen', 'Wollen Sie diesen Eintrag wirklich löschen:\n\n"' + this.tree.getSelectionModel().getSelectedNode().text + '"',
            function  (button) {
                if (button == 'yes') {
                    Ext.Ajax.request({
                        url: this.controllerUrl + 'jsonDelete',
                        params: {
                            id: this.tree.getSelectionModel().getSelectedNode().id
                        },
                        success: function(r) {
                            response = Ext.decode(r.responseText);
                            node = this.tree.getNodeById(response.id);
                            if (node.nextSibling) {
                                sibling = node.nextSibling;
                            } else if (node.previousSibling) {
                                sibling = node.previousSibling;
                            } else if (node.parentNode) {
                                sibling = node.parentNode;
                            }
                            this.tree.getSelectionModel().select(sibling);
                            node.parentNode.removeChild(node);
                        },
                        scope: this
                    });
                }
            },
            this
        );
    },

    onMove : function(e){
        Ext.Ajax.request({
            url: this.controllerUrl + 'jsonMove',
            params: {
                source: e.dropNode.id,
                target: e.target.id,
                point: e.point
            },
            failure: function(r) {
                this.tree.getRootNode().reload();
            },
            scope: this
        })
        return true;
    },

    onCollapse : function(node) {
        Ext.Ajax.request({
            url: this.controllerUrl + 'jsonCollapse',
            params: {id: node.id}
        });
    },

    onExpand : function(node) {
        if (!node.attributes.children) {
            Ext.Ajax.request({
                url: this.controllerUrl + 'jsonExpand',
                params: {id: node.id}
            });
        }
    },

    onVisible : function (o, e) {
        Ext.Ajax.request({
            url: this.controllerUrl + 'jsonVisible',
            params: {
                id: this.tree.getSelectionModel().getSelectedNode().id
            },
            success: function(r) {
                response = Ext.decode(r.responseText);
                node = this.tree.getNodeById(response.id);
                node.attributes.visible = response.visible;
                this.setVisible(node);
            },
            scope: this
        })
    },

    setVisible : function (node) {
        if (node.attributes.visible) {
            node.ui.iconNode.style.backgroundImage = 'url(/assets/vps/images/silkicons/' + this.icons['default'] + '.png)';
        } else {
            node.ui.iconNode.style.backgroundImage = 'url(/assets/vps/images/silkicons/' + this.icons['invisible'] + '.png)';
        }
    },

    onEdit : function (o, e) {
        this.fireEvent('edit', this.tree.getSelectionModel().getSelectedNode());
    }

});
