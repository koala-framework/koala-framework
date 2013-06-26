Ext.namespace('Kwf.Component');
Kwf.Component.Pages = Ext.extend(Ext.Panel, {
    initComponent : function()
    {
        this.actions = {};
        this.componentConfigs = {};

        this.pageButtonMenu = new Ext.menu.Menu({
            items    : [
                '-',
                this.getAction('properties'),
                this.getAction('add'),
                this.getAction('delete'),
                this.getAction('copy'),
                this.getAction('paste'),
                this.getAction('visible'),
                this.getAction('makeHome'),
                this.getAction('preview')
            ]
        });
        this.pageButton = new Ext.Toolbar.Button({
            cls     : 'x-btn-text-icon bmenu',
            text    : trlKwf('Page'),
            menu    : this.pageButtonMenu,
            icon    : '/assets/silkicons/page.png',
            disabled: true
        });

        this.treePanel = new Kwf.Auto.TreePanel({
            controllerUrl: '/admin/component/pages',
            title       : trlKwf('Page tree'),
            region      : 'west',
            split       : true,
            width       : 300,
            collapsible : true,
            minSize     : 200,
            maxSize     : 600,
            autoScroll: true,
            tbar: []
        });
        
        var tbar = this.treePanel.getTopToolbar();
        tbar.add(this.pageButton);

        tbar.add('-');
        tbar.add(trlKwf('Search: '));
        
        var filter = new Kwf.Auto.Filter.Text({name: 'text', 'paramName': 'query'});
        filter.on('filter', function(f, params) {
            this.applyBaseParams(params);
            this.reload();
        }, this.treePanel);
        filter.getToolbarItem().each(function(i) {
            tbar.add(i);
        });
        
        tbar.add('->');
        tbar.add(this.treePanel.getAction('reload'));
        
        this.treePanel.onMoved = function (response) {
            this.tree.getRootNode().reload();
        };

        this.contentTabPanel = new Ext.TabPanel({
            region      : 'center'
        });
        this.contentTabPanel.on('render', function() {
            this.contentTabPanel.strip.setHeight(24);
        }, this);

        this.layout = 'border';
        this.items = [this.treePanel, this.contentTabPanel];

        Kwf.Component.Pages.superclass.initComponent.call(this);

        this.treePanel.on('loaded', this.onTreePanelLoaded, this);
        this.setupEditform();

        this.editActions = {};
    },
    setupEditform : function ()
    {
       this.editDialog = new Kwf.Auto.Form.Window({
            width: 620,
            height: 400,
            controllerUrl: '/admin/component/page'
        });
        this.editDialog.on('datachange', function(test) {
            this.treePanel.tree.root.reload();
        }, this);
        this.editDialog.on('renderform', function() {
            var component = this.editDialog.getAutoForm().findField('component');
            if (component && component.formsForComponent) {
                //initially hide all to avoid flickr
                this.editDialog.getAutoForm().cascade(function(i) {
                    if (i.showDependingOnComponent) {
                        i.disableRecursive(); //to disable validation
                        i.hide();
                    }
                }, this);
                component.on('changevalue', function() {
                    //hide/show forms depending on selected component
                    this.editDialog.getAutoForm().cascade(function(i) {
                        var showForms = component.formsForComponent[component.getValue()] || [];
                        if (i.showDependingOnComponent) {
                            if (showForms.indexOf(i.name) !== -1) {
                                i.show();
                                i.enableRecursive();
                            } else {
                                i.disableRecursive(); //to disable validation
                                i.hide();
                            }
                        }
                    }, this);
                }, this);
            }
        }, this);
    },

    onTreePanelLoaded : function(tree)
    {
        tree.loader.on('load', function(tree, node, response) {
            var result = Ext.decode(response.responseText);
            Ext.applyIf(this.componentConfigs, result.componentConfigs);
        }, this);

        this.contextMenu = new Ext.menu.Menu({
             items: [
                '-',
                this.getAction('properties'),
                this.getAction('add'),
                this.getAction('delete'),
                this.getAction('copy'),
                this.getAction('paste'),
                this.getAction('visible'),
                this.getAction('makeHome'),
                this.getAction('preview')
            ]
        });

        tree.on('click', this.treeSelectionchange, this);
        tree.on('contextmenu', function (node) {
            node.select();
            this.treeSelectionchange(node);
            this.contextMenu.show(node.ui.getAnchor());
        }, this);

        tree.on('dblclick', function (o, e) {
            var i = this.contextMenu.items.first();
            if (i && i.handler) {
                i.handler.call(i.scope, i, e);
            }
        }, this);

    },

    treeSelectionchange : function (node) {
        if (!node) return;
        var data = node.attributes;
        
        if (data.disabled) {
            this.pageButton.disable();
        } else {
            this.pageButton.enable();
        }
        
        for (var action in data.actions) {
            if (data.actions[action]) {
                this.getAction(action).enable();
            } else {
                this.getAction(action).disable();
            }
        }

        this.contextMenu.items.each(function(i) {
            if (i.initialConfig.actionKey) {
                this.contextMenu.remove(i);
            }
        }, this);
        this.pageButtonMenu.items.each(function(i) {
            if (i.initialConfig.actionKey) {
                this.pageButtonMenu.remove(i);
            }
        }, this);
        var actionsAdded = 0;
        data.editComponents.each(function(editComponent) {
            var editKey = editComponent.componentId;
            var actionKey = editComponent.componentClass+'-'+editComponent.type;
            if (!this.editActions[editKey+actionKey]) {
                this.editActions[editKey+actionKey] = new Ext.Action({
                    text    : this.componentConfigs[actionKey].title,
                    handler : function (o, e) {
                        var node = this.treePanel.tree.getSelectionModel().getSelectedNode();
                        node.attributes.editComponents.each(function(editComponent) {
                            if (editComponent.componentId+editComponent.componentClass+'-'+editComponent.type == o.editKey+o.actionKey) {
                                this.loadComponent({
                                    id: editComponent.componentId,
                                    componentClass: editComponent.componentClass,
                                    type: editComponent.type,
                                    text: node.text,
                                    icon: node.attributes.bIcon,
                                    editComponents: node.attributes.editComponents,
                                    pageId: node.attributes.id
                                });
                                return false;
                            }
                        }, this);
                    },
                    icon    : this.componentConfigs[actionKey].icon,
                    cls     : 'x-btn-text-icon',
                    scope   : this,
                    actionKey: actionKey,
                    editKey: editKey
                });
            }
            this.editActions[editKey+actionKey].setDisabled(data.disabled);
            this.contextMenu.insert(actionsAdded, new Ext.menu.Item(this.editActions[editKey+actionKey]));
            this.pageButtonMenu.insert(actionsAdded, new Ext.menu.Item(this.editActions[editKey+actionKey]));
            actionsAdded++;
        }, this);
    },

    loadComponent: function(data)
    {
        var panel = this.contentTabPanel.getItem('page'+data.pageId);
        if (!panel) {
            panel = this.createComponentPanel(data);
            this.contentTabPanel.add(panel);
        }
        this.contentTabPanel.setActiveTab(panel);

        panel.applyBaseParams({
            id: data.id
        });
        panel.mainComponentText = data.text;
        panel.mainComponentIcon = data.icon;
        panel.load({
            componentClass: data.componentClass,
            type: data.type,
            editComponents: data.editComponents
        });
    },

    createComponentPanel: function(data)
    {
        var panel = new Kwf.Component.ComponentPanel({
            id          : 'page'+data.pageId,
            title       : data.text,
            closable    : true,
            autoLoad    : false,
            componentConfigs : this.componentConfigs
        });
        return panel;
    },

    _removeEditDialogForm: function()
    {
        var form = this.editDialog.getAutoForm();
        if (form.formPanel) {
            form.remove(form.formPanel, true);
            this.editDialog.getAutoForm().formPanel = null;
        }
    },

    getAction : function(type)
    {
        if (this.actions[type]) return this.actions[type];

        if (type == 'properties') {
            this.actions[type] = new Ext.Action({
                text    : trlKwf('Page properties'),
                handler : function () {
                    this._removeEditDialogForm();
                    var node = this.treePanel.tree.selModel.selNode;
                    this.editDialog.getAutoForm().setBaseParams({
                        componentId: node.attributes.editControllerComponentId
                    });
                    this.editDialog.showEdit(node.id);
                },
                icon    : '/assets/silkicons/page_gear.png',
                cls     : 'x-btn-text-icon',
                scope   : this
            });
        } else if (type == 'add') {
            this.actions[type] = new Ext.Action({
                text    : trlKwf('Add new child page'),
                handler : function () {
                    this._removeEditDialogForm();
                    var node = this.treePanel.tree.selModel.selNode;
                    this.editDialog.getAutoForm().setBaseParams({
                        componentId: node.attributes.editControllerComponentId,
                        parent_id: node.id
                    });
                    this.editDialog.showAdd();
                },
                icon    : '/assets/silkicons/page_add.png',
                cls     : 'x-btn-text-icon',
                scope   : this
            });
        } else if (type == 'delete') {
            this.actions[type] = new Ext.Action({
                text    : trlKwf('Delete Page'),
                handler : function() {
                    this.treePanel.onDelete();
                },
                icon    : '/assets/silkicons/page_delete.png',
                cls     : 'x-btn-text-icon',
                scope   : this
            });
        } else if (type == 'copy') {
            this.actions[type] = new Ext.Action({
                text    : trlKwf('Copy Page'),
                handler : function() {
                    this.onCopy();
                },
                icon    : '/assets/silkicons/page_copy.png',
                cls     : 'x-btn-text-icon',
                scope   : this
            });
        } else if (type == 'paste') {
            this.actions[type] = new Ext.Action({
                text    : trlKwf('Paste Page'),
                handler : function() {
                    this.onPaste();
                },
                icon    : '/assets/silkicons/page_paste.png',
                cls     : 'x-btn-text-icon',
                scope   : this
            });
        } else if (type == 'visible') {
            this.actions[type] = new Ext.Action({
                text    : trlKwf('Show / Hide Page'),
                handler : function() {
                    this.treePanel.onVisible();
                },
                icon    : '/assets/fx_invisible/silkicons/page.png',
                cls     : 'x-btn-text-icon',
                scope   : this
            });
        } else if (type == 'makeHome') {
            this.actions[type] = new Ext.Action({
                text    : trlKwf('Use as homepage'),
                handler : function (o, e) {
                    Ext.Msg.show({
                        title: trlKwf('Use as homepage'),
                        msg: trlKwf('Attention! You are about to set the selected page as the homepage of your website. This may affect the entire website. Do you wish to proceed?'),
                        buttons: Ext.Msg.YESNO,
                        icon: Ext.MessageBox.WARNING,
                        fn: function(btn, text) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    url: '/admin/component/pages/json-make-home',
                                    success: function(r) {
                                        response = Ext.decode(r.responseText);
                                        var oldhome = this.treePanel.tree.getNodeById(response.oldhome);
                                        oldhome.attributes.visible = response.oldhomeVisible;
                                        this.treePanel.setVisible(oldhome);
                                        oldhome.ui.iconNode.style.backgroundImage = 'url(/assets/silkicons/page.png)';
                                        var home = this.treePanel.tree.getNodeById(response.home);
                                        home.ui.iconNode.style.backgroundImage = 'url(/assets/silkicons/application_home.png)';
                                    },
                                    params: {id: this.treePanel.tree.getSelectionModel().getSelectedNode().id },
                                    scope: this
                                });
                            }
                        },
                        scope: this
                    });
                },
                icon    : '/assets/silkicons/application_home.png',
                cls     : 'x-btn-text-icon',
                scope   : this
            });
        } else if (type == 'expand') {
            this.actions[type] = new Ext.Action({
                text    : trlKwf('Expand here'),
                handler : function () { this.treePanel.tree.getSelectionModel().getSelectedNode().expand(true); },
                icon    : '/assets/silkicons/bullet_add.png',
                cls     : 'x-btn-text-icon',
                scope   : this
            });
        } else if (type == 'collapse') {
            this.actions[type] = new Ext.Action({
                text    : trlKwf('Collapse here'),
                handler : function () { this.treePanel.tree.getSelectionModel().getSelectedNode().collapse(true); },
                icon    : '/assets/silkicons/bullet_delete.png',
                cls     : 'x-btn-text-icon',
                scope   : this
            });
        } else if (type == 'preview') {
            this.actions[type] = new Ext.Action({
                text    : trlKwf('Open preview'),
                handler : function () {
                    window.open('/admin/component/pages/open-preview?page_id='+
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
    },

    onCopy: function(o, e) {
        Ext.Ajax.request({
            url: this.treePanel.controllerUrl + '/json-copy',
            params: Ext.apply({id:this.treePanel.getSelectedId()}, this.treePanel.getBaseParams()),
            mask: this.el,
            success: function(response, options, result) {
            },
            scope: this
        });
    },

    onPaste: function() {
        Ext.Ajax.request({
            url: this.treePanel.controllerUrl + '/json-paste',
            params: Ext.apply({id:this.treePanel.getSelectedId()}, this.treePanel.getBaseParams()),
            timeout: 5*60*1000,
            progress: true,
            progressTitle : trlKwf('Paste Page'),
            showCancel: false,
            success: function(response, options, result) {
                this.treePanel.reload();
            },
            scope: this
        });
    }

});

Ext.reg('kwf.component.pages', Kwf.Component.Pages);
