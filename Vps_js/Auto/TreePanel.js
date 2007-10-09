Ext.namespace('Vps.Auto');
Vps.Auto.TreePanel = Ext.extend(Ext.Panel, {
    initComponent : function()
    {
	    this.addEvents({
	        selectionchange: true,
	        editaction: true,
            addaction: true,
	        generatetoolbar: true,
	        generatetoolbarstart: true,
	        loaded: true
	    });

	    Ext.Ajax.request({
	        url: this.controllerUrl + 'jsonMeta',
	        params: this.baseParams,
	        success: this.onMetaChange,
	        scope: this
	    });
        this.actions = {};
        this.autoScroll = true;

        Vps.Auto.TreePanel.superclass.initComponent.call(this);
    },

    onMetaChange: function(response) {
        meta = Ext.decode(response.responseText);
        this.icons = meta.icons;

        // Toolbar
        if (meta.buttons.each == undefined) { // Abfrage nötig, falls keine Buttons geliefert
            var tbar = [];
            for (var button in meta.buttons) {
                tbar.add(this.getAction(button));
            }
        }

        // Tree
        this.tree = new Ext.tree.TreePanel({
            border      : false,
            animate     : true,
            loader      : new Ext.tree.TreeLoader({dataUrl: this.controllerUrl + 'jsonData'}),
            enableDD    : meta.enableDD,
            containerScroll: true,
            rootVisible : meta.rootVisible,
            tbar        : tbar
        });

        this.tree.setRootNode(
            new Ext.tree.AsyncTreeNode({
                text: meta.rootText,
                id: '0',
                allowDrag: false
            })
        );

        this.tree.getSelectionModel().on('selectionchange', this.onSelectionchange, this);
        this.tree.on('beforenodedrop', this.onMove, this);
        this.tree.on('collapsenode', this.onCollapseNode, this);
        this.tree.on('expandnode', this.onExpandNode, this);

        this.add(this.tree);
        this.doLayout();

        if (meta.rootVisible) {
            this.tree.getRootNode().ui.iconNode.style.backgroundImage = 'url(/assets/vps/images/silkicons/' + meta.icons.root + '.png)';
            this.tree.getRootNode().select();
        }
        this.tree.getRootNode().expand();

        if (!this.editDialog && meta.editDialog) {
            this.editDialog = meta.editDialog;
        }
        if (this.editDialog && !(this.editDialog instanceof Vps.Auto.Form.Window)) {
            this.editDialog = new Vps.Auto.Form.Window(meta.editDialog);
        }
        if (this.editDialog) {
            this.editDialog.on('datachange', function(o) {
                if (o.data.addedId != undefined) {
                    id = o.data.addedId;
                } else {
                    id = this.tree.getSelectionModel().getSelectedNode().id;
                }
                this.onSave(id);
            }, this);
            this.tree.on('dblclick', function(grid, rowIndex) {
                this.onEdit();
            }, this);
        }

        this.fireEvent('loaded', this.tree);
    },

    onEdit : function (o, e) {
        if (this.editDialog != undefined) {
            node = this.tree.getSelectionModel().getSelectedNode();
            this.editDialog.showEdit(node.id);
        } else {
            this.fireEvent('editaction', this.tree.getSelectionModel().getSelectedNode());
        }
    },

    onAdd: function (o, e) {
        if (this.editDialog != undefined) {
            this.editDialog.showEdit(0);
            this.editDialog.getForm().baseParams.parent_id = this.tree.getSelectionModel().getSelectedNode().id;
        } else {
            this.fireEvent('addaction', this.tree.getSelectionModel().getSelectedNode());
        }
    },

    onSave : function (id)
    {
        Ext.Ajax.request({
            url: this.controllerUrl + 'jsonNodeData',
            params: { node: id },
            success: function(r) {
                var response = Ext.decode(r.responseText).data;
                node = this.tree.getNodeById(response.id);
                if (node == undefined) {
                    if (response.data.parent_id == null) { response.data.parent_id = 0; }
                    parentNode = this.tree.getNodeById(response.data.parent_id);
                    if (parentNode.isLoaded()) {
                        response.uiProvider = eval(response.uiProvider);
                        node = new Ext.tree.AsyncTreeNode(response);
                        if (parentNode.firstChild) {
                            parentNode.insertBefore(node, parentNode.firstChild);
                        } else {
                            parentNode.appendChild(node);
                        }
                        parentNode.expand();
                        this.tree.getSelectionModel().select(this.tree.getNodeById(response.id));
                    } else {
                        parentNode.expand();
                    }
                } else {
                    node.setText(response.text);
                    node.attributes.visible = response.visible;
                    this.setVisible(node);
                }
            },
            scope: this
        })
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

    onCollapseNode : function(node) {
        Ext.Ajax.request({
            url: this.controllerUrl + 'jsonCollapse',
            params: {id: node.id}
        });
    },

    onExpandNode : function(node) {
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
        } else if (type == 'expandAll') {
            this.actions[type] = new Ext.Action({
                text    : '',
                handler : function () { this.tree.expandAll(); },
                icon    : '/assets/vps/images/silkicons/bullet_add.png',
                cls     : 'x-btn-icon',
                scope   : this
            });
        } else if (type == 'collapseAll') {
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
    }

});

Vps.Auto.TreeNode = Ext.extend(Ext.tree.TreeNodeUI, {
    initEvents : function(){
        Vps.Auto.TreeNode.superclass.initEvents.call(this);
        this.node.ui.iconNode.style.backgroundImage = 'url(/assets/vps/images/silkicons/' + this.node.attributes.bIcon + '.png)';
    },
    onDblClick : function(e){
        e.preventDefault();
        this.fireEvent("dblclick", this.node, e);
    }
});

