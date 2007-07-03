Vps.AutoTree = function(renderTo, config)
{
    Ext.apply(this, config);
    this.renderTo = renderTo;
    this.events = {
        selectionchange: true
    };

    Ext.Ajax.request({
        url: this.controllerUrl + 'jsonMeta',
        success: this.init,
        scope: this
    })
};

Ext.extend(Vps.AutoTree, Ext.util.Observable,
{
    init: function(response) {
        r = Ext.decode(response.responseText);
        this.icons = r.icons;
        
        // Icons
        var st = '';
        for (var key in r.icons) {
            if (key != 'add' && key != 'delete') {
                st += '.' + key + ' .x-tree-node-icon {\n background-image:url(/assets/vps/images/silkicons/' + r.icons[key] + '.png);\n }\n\n'
            }
        }
        Ext.DomHelper.overwrite('treeStyles', st);

        // Toolbar
        toolbar = new Ext.Toolbar(Ext.get(this.renderTo).createChild());
        if (r.buttons['add']) {
            this.addButton = toolbar.addButton({
                text    : 'Hinzufügen',
                handler : this.add,
                icon : '/assets/vps/images/silkicons/' + r.icons['add'] + '.png',
                cls: "x-btn-text-icon",
                scope   : this
            });
        }
        if (r.buttons['delete']) {
            this.deleteButton = toolbar.addButton({
                text    : 'Löschen',
                handler : this.del,
                icon : '/assets/vps/images/silkicons/' + r.icons['delete'] + '.png',
                cls: "x-btn-text-icon",
                disabled: true,
                scope   : this
            });
        }
        if (r.buttons['invisible']) {
            this.visibleButton = toolbar.addButton({
                text    : 'Unsichtbar',
                handler : this.visible,
                disabled: true,
                enableToggle : true,
                icon : '/assets/vps/images/silkicons/' + r.icons['invisible'] + '.png',
                cls: "x-btn-text-icon",
                scope   : this
            });
        }
        if (r.buttons['reload']) {
            toolbar.addButton({
                text    : '',
                handler : function () { this.tree.getRootNode().reload(); },
                icon : '/assets/vps/images/silkicons/' + r.icons['reload'] + '.png',
                cls: "x-btn-icon",
                scope   : this
            });
        }
        this.toolbar = toolbar;

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
                allowDrag: false,
                cls: 'root'
            })
        );

        this.tree.getSelectionModel().on('selectionchange', this.selectionchange, this);
        this.tree.on('beforenodedrop', this.move, this);
        this.tree.on('collapse', this.collapse, this);
        this.tree.on('expand', this.expand, this);

        this.tree.render();
        if (r.rootVisible) {
            this.tree.getRootNode().select();
        }
        this.tree.getRootNode().expand();

    },

    selectionchange: function (e, node) {
        if (node && node.id != 0) {
            if (this.visibleButton) {
                this.visibleButton.enable();
                this.setvisible(node);
            }
            if (this.deleteButton) {
                this.deleteButton.enable();
            }
        } else {
            if (this.visibleButton) {
                this.visibleButton.toggle(false);
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
                id: this.tree.getSelectionModel().getSelectedNode().id,
                visible: !this.visibleButton.pressed
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
            this.visibleButton.toggle(false);
            node.ui.removeClass('invisible');
            node.ui.addClass('default');
        } else {
            this.visibleButton.toggle(true);
            node.ui.removeClass('default');
            node.ui.addClass('invisible');
        }
    }
    
});
