Ext.namespace('Kwc.Directories.Item.Directory');
Kwc.Directories.Item.Directory.TabsPanel = Ext.extend(Kwf.Binding.ProxyPanel,
{
    initComponent: function()
    {
        this.layout = 'border';
        
        this.grid = new Kwf.Auto.GridPanel(Ext.applyIf({
            controllerUrl: this.controllerUrl,
            region: 'west',
            width: this.width,
            split: true
        }, this.gridConfig));

        if (this.componentPlugins) {
            this.plugins = [ ];
            this.componentPlugins.each(function(v) {
                var pluginClass = eval(v.pluginClass);
                var plugin = new pluginClass(v);
                this.plugins.push(plugin);
            }, this);
        }

        this.initEditPanels();

        this.tabs = new Ext.TabPanel({
            region: 'center',
            activeTab: 0,
            items: this.editPanels
        });
        this.proxyItem = this.grid;
        this.items = [this.grid, this.tabs];

        Kwc.Directories.Item.Directory.TabsPanel.superclass.initComponent.call(this);
    },

    initEditPanels: function() {
        this.editPanels = [];
        if (!this.hideDetailsController) {
            this.detailsForm = Ext.ComponentMgr.create(Ext.applyIf({
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

Ext.reg('kwc.directories.item.directory.tabs', Kwc.Directories.Item.Directory.TabsPanel);
