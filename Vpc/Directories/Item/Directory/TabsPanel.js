Ext.namespace('Vpc.Directories.Item.Directory');
Vpc.Directories.Item.Directory.TabsPanel = Ext.extend(Vps.Binding.ProxyPanel,
{
    initComponent: function()
    {
        this.layout = 'border';
        
        this.grid = new Vps.Auto.GridPanel({
            controllerUrl: this.controllerUrl,
            region: 'west',
            width: this.width,
            split: true
        });

        if (this.componentPlugins) {
            this.plugins = [ ];
            this.componentPlugins.each(function(v) {
                var pluginClass = eval(v.pluginClass);
                var plugin = new pluginClass(v);
                this.plugins.push(plugin);
            }, this);
        }

        this.detailsForm = new Vpc.Directories.Item.Directory.EditFormPanel({
            controllerUrl: this.detailsControllerUrl,
            title: trlVps('Details')
        });
        this.grid.addBinding(this.detailsForm);

        var editPanels = [this.detailsForm];
        this.contentEditComponents.each(function(ec) {
            editPanels.push(Vps.Binding.AbstractPanel.createFormOrComponentPanel(
                this.componentConfigs, ec, {}, this.grid
            ));
        }, this);

        var tabs = new Ext.TabPanel({
            region: 'center',
            activeTab: 0,
            items: editPanels
        });
        this.proxyItem = this.grid;
        this.items = [this.grid, tabs];

        Vpc.Directories.Item.Directory.TabsPanel.superclass.initComponent.call(this);
    },
    applyBaseParams: function(baseParams) {
        if (baseParams.id) delete baseParams.id;
        this.detailsForm.setBaseParams(baseParams);
        return Vpc.Directories.Item.Directory.TabsPanel.superclass.applyBaseParams.apply(this, arguments);
    },
    setBaseParams : function(baseParams) {
        if (baseParams.id) delete baseParams.id;
        this.detailsForm.setBaseParams(baseParams);
        return Vpc.Directories.Item.Directory.TabsPanel.superclass.setBaseParams.apply(this, arguments);
    }

});

Ext.reg('vpc.directories.item.directory.tabs', Vpc.Directories.Item.Directory.TabsPanel);
