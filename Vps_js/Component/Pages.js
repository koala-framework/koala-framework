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
    this.tree.on('generatetoolbarstart', function(o, e) {
        this.tree.editButton = this.tree.toolbar.addButton({
            tooltip: 'Bearbeiten',
            disabled: true,
            handler : 
                function (o, e) {
                    node = this.tree.getSelectionModel().getSelectedNode();
                    this.fireEvent('editcomponent', {id: node.attributes.id, cls: node.attributes.data.component_class, text: node.text});
                },
            icon : '/assets/vps/images/silkicons/page_edit.png',
            cls: "x-btn-icon",
            scope   : this.tree
        });
        this.tree.propertiesButton = this.tree.toolbar.addButton({
            disabled: true,
            tooltip    : 'Eigenschaften',
            handler :
                function (o, e) {
                    this.editform.load(this.tree.tree.getSelectionModel().getSelectedNode().id);
                    this.editform.show();
                },
            icon : '/assets/vps/images/silkicons/page_gear.png',
            cls: "x-btn-icon",
            scope   : this
        })
    }, this);

    this.tree.on('selectionchange', this.treeSelectionchange, this.tree);
    this.tree.on('editcomponent', this.loadComponent, this);

    this.created = new Array();
}

Ext.extend(Vps.Component.Pages, Ext.util.Observable,
{
    getPanel : function()
    {
        //Vps.mainLayout.getRegion('center').remove(Vps.mainLayout.getRegion('center').getActivePanel(), false)
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
