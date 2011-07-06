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
            width: this.width,
            split: true
        });

        this.detailsForm = new Vps.Auto.FormPanel({
            controllerUrl: this.detailsControllerUrl,
            title: trlVps('Details')
        });
        grid.addBinding(this.detailsForm);

        this.editPanels = [this.detailsForm];
        this.contentEditComponents.each(function(ec) {
            var componentConfig = this.componentConfigs[ec.componentClass+'-'+ec.type];
            if (componentConfig.needsComponentPanel) {
                var panel = new Vps.Component.ComponentPanel({
                    title: componentConfig.title,
                    mainComponentClass: ec.componentClass,
                    mainType: ec.type,
                    mainComponentId: ec.idTemplate+ec.componentIdSuffix,
                    componentConfigs: this.componentConfigs,
                    mainEditComponents: [ec]
                });
                grid.addBinding(panel);
            } else {
                var panel = Ext.ComponentMgr.create(componentConfig);
                grid.addBinding({
                    item: panel,
                    componentId: ec.idTemplate+ec.componentIdSuffix
                });
            }
            this.editPanels.push(panel);
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
    }

});

Ext.reg('vpc.directories.item.directory.tabs', Vpc.Directories.Item.Directory.TabsPanel);
