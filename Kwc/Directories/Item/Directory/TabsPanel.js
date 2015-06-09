Ext2.namespace('Kwc.Directories.Item.Directory');
Kwc.Directories.Item.Directory.TabsPanel = Ext2.extend(Kwf.Binding.ProxyPanel,
{
    initComponent: function()
    {
        this.layout = 'border';

        this.grid = new Kwc.Directories.Item.Directory.GridPanel(Ext2.applyIf({
            controllerUrl: this.controllerUrl,
            region: 'west',
            width: this.width,
            split: true,
            baseParams: this.baseParams,
            multiFileUpload: this.multiFileUpload
        }, this.gridConfig));
        this.grid.on('selectionchange', this._displayTabsUsedByTheSelectedRow, this);
        this.grid.on('load', this._displayTabsUsedByTheSelectedRow, this);

        if (this.componentPlugins) {
            this.plugins = [ ];
            this.componentPlugins.each(function(v) {
                var pluginClass = eval(v.pluginClass);
                var plugin = new pluginClass(v);
                this.plugins.push(plugin);
            }, this);
        }

        this.initEditPanels();

        this.tabs = new Ext2.TabPanel({
            region: 'center',
            activeTab: 0,
            items: this.editPanels
        });
        if (this.hasMultipleDetailComponents) {
            this.tabs.on('render', function(){
                for (var i=1; i<this.editPanels.length; i++) {
                    this.tabs.hideTabStripItem(i);
                }
            }, this);
        }

        this.proxyItem = this.grid;
        this.items = [this.grid, this.tabs];

        Kwc.Directories.Item.Directory.TabsPanel.superclass.initComponent.call(this);
    },
    _displayTabsUsedByTheSelectedRow: function()
    {
        if (this.hasMultipleDetailComponents
            && this.grid.grid.getSelected()
            && this.grid.grid.getSelected().get('component')
        ) {
            this.editPanels.each(function(panel){
                var componentType = this.grid.grid.getSelected().get('component');
                if (panel.componentType
                    && panel.componentType != componentType) {
                    this.tabs.hideTabStripItem(panel);
                    if (this.tabs.getActiveTab() == panel) {
                        this.tabs.setActiveTab(0);
                    }
                } else {
                    this.tabs.unhideTabStripItem(panel);
                }
            }, this);
        }
    },
    initEditPanels: function() {
        this.editPanels = [];
        if (!this.hideDetailsController) {
            this.detailsForm = Ext2.ComponentMgr.create(Ext2.applyIf({
                xtype: this.detailsXtype,
                controllerUrl: this.detailsControllerUrl,
                title: trlKwf('Details')
            }, this.details));
            this.grid.addBinding(this.detailsForm);
            this.editPanels.push(this.detailsForm);
        }
        this.contentEditComponents.each(function(ec) {
            this.editPanels.push(Kwf.Binding.AbstractPanel.createFormOrComponentPanel(
                this.componentConfigs, ec, {}, this.grid
            ));
            this.editPanels[this.editPanels.length-1].componentType = ec.component;
        }, this);
    },
    applyBaseParams: function(baseParams) {
        if (baseParams.id) delete baseParams.id;
        if (this.detailsForm) this.detailsForm.setBaseParams(baseParams);
        return Kwc.Directories.Item.Directory.TabsPanel.superclass.applyBaseParams.apply(this, arguments);
    },
    setBaseParams : function(baseParams) {
        if (baseParams.id) delete baseParams.id;
        if (this.detailsForm) this.detailsForm.setBaseParams(baseParams);
        return Kwc.Directories.Item.Directory.TabsPanel.superclass.setBaseParams.apply(this, arguments);
    }

});

Ext2.reg('kwc.directories.item.directory.tabs', Kwc.Directories.Item.Directory.TabsPanel);
