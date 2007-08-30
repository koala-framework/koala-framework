Ext.namespace('Vps.AutoTree');
Vps.AutoTree.Node = function(node){
    Vps.AutoTree.Node.superclass.constructor.call(this, node);
}

Ext.extend(Vps.AutoTree.Node, Ext.tree.TreeNodeUI, {
    initEvents : function(){
        Vps.AutoTree.Node.superclass.initEvents.call(this);
        this.node.ui.iconNode.style.backgroundImage = 'url(/assets/vps/images/silkicons/' + this.node.attributes.bIcon + '.png)';
    }
});

Vps.Auto.Tree = function(renderTo, config)
{
    Ext.apply(this, config);
    this.renderTo = renderTo;
    this.events = {
        selectionchange: true,
        edit: true,
        generatetoolbar: true,
        generatetoolbarstart: true
    };

    Ext.Ajax.request({
        url: this.controllerUrl + 'jsonMeta',
        params: config.baseParams,
        success: this.init,
        scope: this
    })
};

Ext.extend(Vps.Auto.Tree, Ext.util.Observable,
{
    init: function(response) {
        r = Ext.decode(response.responseText);
        this.icons = r.icons;
        
        // Toolbar
        b = r.buttons;
        if (b['add'] || b['delete'] || b['invisible'] || b['reload']) {
            toolbar = new Ext.Toolbar(Ext.get(this.renderTo).createChild());
            this.toolbar = toolbar;
            this.fireEvent('generatetoolbarstart', this.toolbar);
            if (b['add']) {
                this.addButton = toolbar.addButton({
                    text    : 'Hinzufügen',
                    tooltip : 'Hinzufügen',
                    handler : this.add,
                    icon : '/assets/vps/images/silkicons/' + r.icons['add'] + '.png',
                    cls: "x-btn-text-icon",
                    scope   : this
                });
            }
            if (b['edit']) {
                this.editButton = toolbar.addButton({
                    text    : 'Bearbeiten',
                    handler : this.edit,
                    icon : '/assets/vps/images/silkicons/' + r.icons['edit'] + '.png',
                    cls: "x-btn-text-icon",
                    disabled: true,
                    scope   : this
                });
            }
            if (b['delete']) {
                this.deleteButton = toolbar.addButton({
                    text    : 'Löschen',
                    handler : this.del,
                    icon : '/assets/vps/images/silkicons/' + r.icons['delete'] + '.png',
                    cls: "x-btn-text-icon",
                    disabled: true,
                    scope   : this
                });
            }
            if (b['invisible']) {
                this.visibleButton = toolbar.addButton({
                    text    : 'Unsichtbar',
                    handler : this.visible,
                    disabled: true,
                    icon : '/assets/vps/images/silkicons/' + r.icons['invisible'] + '.png',
                    cls: "x-btn-text-icon",
                    scope   : this
                });
            }
            if (b['reload']) {
                toolbar.addButton({
                    text    : '',
                    handler : function () { this.tree.getRootNode().reload(); },
                    icon : '/assets/vps/images/silkicons/bullet_star.png',
                    cls: "x-btn-icon",
                    scope   : this
                });
            }
            if (b['expand']) {
                toolbar.addButton({
                    text    : '',
                    handler : function () { this.tree.expandAll(); },
                    icon : '/assets/vps/images/silkicons/bullet_add.png',
                    cls: "x-btn-icon",
                    scope   : this
                });
            }
            if (b['collapse']) {
                toolbar.addButton({
                    text    : '',
                    handler : function () { this.tree.collapseAll(); },
                    icon : '/assets/vps/images/silkicons/bullet_delete.png',
                    cls: "x-btn-icon",
                    scope   : this
                });
            }
        }
        this.fireEvent('generatetoolbar', this.toolbar);

        // Tree
        this.tree = new Ext.tree.TreePanel(this.renderTo, {
            animate: true,
            loader: new Ext.tree.TreeLoader({dataUrl: this.controllerUrl + 'jsonData'}),
            enableDD: r.enableDD,
            containerScroll: true,
            rootVisible: r.rootVisible
        });

        this.tree.setRootNode(
            new Ext.tree.AsyncTreeNode({
                text: r.rootText,
                id: '0',
                allowDrag: false
            })
        );

        this.tree.getSelectionModel().on('selectionchange', this.selectionchange, this);
        this.tree.on('beforenodedrop', this.move, this);
        this.tree.on('collapse', this.collapse, this);
        this.tree.on('expand', this.expand, this);

        this.tree.render();
        if (r.rootVisible) {
            this.tree.getRootNode().ui.iconNode.style.backgroundImage = 'url(/assets/vps/images/silkicons/' + r.icons.root + '.png)';
            this.tree.getRootNode().select();
        }
        this.tree.getRootNode().expand();

    },

    selectionchange: function (e, node) {
        if (node && node.id != 0) {
            if (this.editButton) {
                this.editButton.enable();
            }
            if (this.visibleButton) {
                this.visibleButton.enable();
            }
            if (this.deleteButton) {
                this.deleteButton.enable();
            }
        } else {
            if (this.editButton) {
                this.editButton.disable();
            }
            if (this.visibleButton) {
                this.visibleButton.disable();
            }
            if (this.deleteButton) {
                this.deleteButton.disable();
            }
        }
        this.fireEvent('selectionchange', node);
    },
    
    add: function (o, e) {
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
                        scope: this
                    })
                }
            },
            this
        );
    },
    
    del: function (o, e) {
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
                    })
                }
            },
            this
        );
    },

    move : function(e){
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

    collapse : function(node) {
        Ext.Ajax.request({
            url: this.controllerUrl + 'jsonCollapse',
            params: {id: node.id}
        });
    },
    
    expand : function(node) {
        if (!node.attributes.children) {
            Ext.Ajax.request({
                url: this.controllerUrl + 'jsonExpand',
                params: {id: node.id}
            });
        }
    },
    
    visible : function (o, e) {
        Ext.Ajax.request({
            url: this.controllerUrl + 'jsonVisible',
            params: {
                id: this.tree.getSelectionModel().getSelectedNode().id
            },
            success: function(r) {
                response = Ext.decode(r.responseText);
                node = this.tree.getNodeById(response.id);
                node.attributes.visible = response.visible;
                this.setvisible(node);
            },
            scope: this
        })
    },
    
    setvisible : function (node) {
        if (node.attributes.visible) {
            node.ui.iconNode.style.backgroundImage = 'url(/assets/vps/images/silkicons/' + this.icons['default'] + '.png)';
        } else {
            node.ui.iconNode.style.backgroundImage = 'url(/assets/vps/images/silkicons/' + this.icons['invisible'] + '.png)';
        }
    },
    
    edit : function (o, e) {
        this.fireEvent('edit', this.tree.getSelectionModel().getSelectedNode());
    }
    
});

