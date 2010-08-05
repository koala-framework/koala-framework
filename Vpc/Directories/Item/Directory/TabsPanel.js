Ext.namespace('Vpc.Directories.Item.Directory');
Vpc.Directories.Item.Directory.TabsPanel = Ext.extend(Vps.Binding.ProxyPanel,
{
    initComponent: function() {

        if (this.componentPlugins) {
            this.plugins = [ ];
            this.componentPlugins.each(function(v) {
                var pluginClass = eval(v.pluginClass);
                var plugin = new pluginClass(v);
                this.plugins.push(plugin);
            }, this);
        }

        this.layout = 'border';
        var grid = new Vps.Auto.GridPanel({
            controllerUrl: this.controllerUrl,
            region: 'west',
            width: 500,
            split: true
        });

        this.detailsForm = new Vps.Auto.FormPanel({
            controllerUrl: this.detailsControllerUrl,
            title: trlVps('Details')
        });

        this.editPanels = [this.detailsForm];
        this.contentEditComponents.each(function(ec) {
            var componentConfig = this.componentConfigs[ec.componentClass+'-'+ec.type];
            this.editPanels.push(new Vps.Component.ComponentPanel({
                title: componentConfig.title,
                mainComponentClass: ec.componentClass,
                mainType: ec.type,
                mainComponentId: ec.idTemplate+ec.componentIdSuffix,
                componentConfigs: this.componentConfigs,
                mainEditComponents: [ec]
            }));
        }, this);

        this.editPanels.each(function(p) {
            grid.addBinding(p);
        }, this);

        var tabs = new Ext.TabPanel({
            region: 'center',
            activeTab: 0,
            items: this.editPanels
        });
        this.proxyItem = grid;
        this.items = [grid, tabs];

        Vpc.Directories.Item.Directory.TabsPanel.superclass.initComponent.call(this);
    },
    applyBaseParams: function(baseParams) {
        this.detailsForm.setBaseParams(baseParams);
        return Vpc.Directories.Item.Directory.TabsPanel.superclass.applyBaseParams.apply(this, arguments);
    },
    setBaseParams : function(baseParams) {
        this.detailsForm.setBaseParams(baseParams);
        return Vpc.Directories.Item.Directory.TabsPanel.superclass.setBaseParams.apply(this, arguments);
    },

});

Ext.reg('vpc.directories.item.directory.tabs', Vpc.Directories.Item.Directory.TabsPanel);
