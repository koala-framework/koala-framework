Vps.Admin.Pages.Tree = function(renderTo, config)
{
    Ext.apply(this, config);
    this.renderTo = renderTo;
    this.events = {
        'selectionchange' : true
    };

    toolbar = new Ext.Toolbar(Ext.get(renderTo).createChild());
    toolbar2 = new Ext.Toolbar(Ext.get(renderTo).createChild());

    this.addButton = toolbar.addButton({
        disabled: true,
        text    : 'Hinzufügen',
        icon : 'add',
        handler : this.addPage,
        icon : '/assets/vps/images/silkicons/page_add.png',
        cls: "x-btn-text-icon",
        scope   : this
    });
    this.deleteButton = toolbar.addButton({
        disabled: true,
        text    : 'Löschen',
        handler : this.deletePage,
        icon : '/assets/vps/images/silkicons/page_delete.png',
        cls: "x-btn-text-icon",
        scope   : this
    });
    this.editButton = toolbar.addButton({
        disabled: true,
        text    : 'Bearbeiten',
        handler : 
            function (o, e) {
                this.fireEvent('editPage', this.tree.getSelectionModel().getSelectedNode());
            },
        icon : '/assets/vps/images/silkicons/page_edit.png',
        cls: "x-btn-text-icon",
        scope   : this
    });
    Ext.get(Ext.get(toolbar.addSpacer().getEl()).dom.parentNode).setStyle('width', '100%');
    this.propertiesButton = toolbar2.addButton({
        disabled: true,
        text    : 'Eigenschaften',
        handler : this.editProperties,
        icon : '/assets/vps/images/silkicons/page_gear.png',
        cls: "x-btn-text-icon",
        scope   : this
    });
    this.onlineButton = toolbar2.addButton({
        disabled: true,
        text    : 'Online',
        enableToggle : true,
        handler : this.online,
        icon : '/assets/vps/images/silkicons/world.png',
        cls: "x-btn-text-icon",
        scope : this
    });
    Ext.get(Ext.get(toolbar2.addSpacer().getEl()).dom.parentNode).setStyle('width', '100%');
    toolbar2.addButton({
        text    : '',
        handler :
            function (o, e) {
                this.tree.getRootNode().reload();
            },
        icon : '/assets/vps/images/silkicons/control_repeat_blue.png',
        cls: "x-btn-icon",
        scope   : this
    });
    
    this.tree = new Ext.tree.TreePanel(renderTo, {
        animate:true, 
        loader: new Ext.tree.TreeLoader({dataUrl:'/admin/pages/jsonGetNodes'}),
        enableDD:true,
        containerScroll: true,
        rootVisible: false
    });

    this.tree.setRootNode(
        new Ext.tree.AsyncTreeNode({
            text: '',
            draggable:false,
            id:'root'
        })
    );

    this.tree.on('beforenodedrop', this.nodedrop, this);
    this.tree.on('collapse', this.collapse, this);
    this.tree.getSelectionModel().on('selectionchange', this.selectionchange, this);
    
    this.tree.render();
    this.tree.getRootNode().expand();
}

Ext.extend(Vps.Admin.Pages.Tree, Ext.util.Observable,
{
    selectionchange: function (e, node) {
        if (node) {
            if (node.attributes.type == 'default') {
                this.onlineButton.enable();
                this.onlineButton.toggle(node.attributes.online);
                this.editButton.enable();
                this.propertiesButton.enable();
                this.addButton.enable();
                this.deleteButton.enable();
            } else if (node.attributes.type == 'root') {
                this.onlineButton.enable();
                this.onlineButton.toggle(node.attributes.online);
                this.editButton.enable();
                this.propertiesButton.enable();
                this.addButton.disable();
                this.deleteButton.disable();
            } else {
                this.onlineButton.disable();
                this.onlineButton.toggle(true);
                this.propertiesButton.disable();
                this.editButton.disable();
                this.addButton.enable();
                this.deleteButton.disable();
            }
        }
    },
    
    collapse : function(e, node) {
        Ext.Ajax.request({
            url: '/admin/pages/jsonCollapseNode',
            params: {id: e.id}
        });
    },
    
    nodedrop : function(e){
        if (e.target.attributes.type != 'default') {
            return false;
        } else {
            Ext.Ajax.request({
                url: '/admin/pages/jsonMovePage',
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
        }
    },

    online : function (o, e) {
        Ext.Ajax.request({
            url: '/admin/pages/jsonOnline',
            params: {
                id: this.tree.getSelectionModel().getSelectedNode().id,
                online: this.onlineButton.pressed
            },
            success: function(r) {
                response = Ext.decode(r.responseText);
                this.setOnline(response.online);
            },
            failure: function(r) {
                response = Ext.decode(r.responseText);
                this.setOnline(response.online);
            },
            scope: this
        })
    },
    
    setOnline: function (online) {
        this.onlineButton.toggle(online);
        node = this.tree.getSelectionModel().getSelectedNode();
        node.attributes.online = online;
        if (online) {
            node.ui.removeClass('offline');
        } else {
            node.ui.addClass('offline');
        }
    },
    
    deletePage : function (o, e) {
        Ext.MessageBox.confirm('Löschen', 'Wollen Sie die Seite wirklich löschen?', 
            function  (button) {
                if (button == 'yes') {
                    Ext.Ajax.request({
                        url: '/admin/pages/jsonDeletePage',
                        params: { id: this.tree.getSelectionModel().getSelectedNode().id },
                        success: function(r) {
                            node = this.tree.getSelectionModel().getSelectedNode();
                            nextNode = node.nextSibling;
                            node.parentNode.removeChild(node);
                            this.tree.getSelectionModel().select(nextNode);
                        },
                        scope: this
                    })
                }
            },
            this
        );
    },

    addPage : function (o, e) {
        Ext.MessageBox.prompt('Hinzufügen', 'Geben Sie einen Seitennamen ein.', 
            function  (button, name) {
                if (button == 'ok') {
                    Ext.Ajax.request({
                        url: '/admin/pages/jsonAddPage',
                        params: { 
                            parentId: this.tree.getSelectionModel().getSelectedNode().id,
                            name: name
                        },
                        success: function(r) {
                            response = Ext.decode(r.responseText);
                            node = new Ext.tree.AsyncTreeNode(response.config);
                            this.tree.getSelectionModel().getSelectedNode().appendChild(node);
                            this.tree.getSelectionModel().select(this.tree.getNodeById(response.config.id));
                        },
                        scope: this
                    })
                }
            },
            this
        );
    },
    
    editProperties : function (o, e) {
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
                        node = this.tree.getSelectionModel().getSelectedNode();
                        node.setText(a.result.name);
                        this.setOnline(a.result.online);
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
    
});
