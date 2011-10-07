Ext.namespace('Vpc.Directories.Item.Directory');
Vpc.Directories.Item.Directory.Panel = Ext.extend(Vps.Auto.GridPanel,
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

        this.on('cellclick', function(grid, rowIndex, columnIndex, e) {
            var col = grid.getColumnModel().config[columnIndex];
            if (col.columnType != 'editContent') return;

            var row = grid.getStore().getAt(rowIndex);
            var componentId = col.editIdTemplate.replace('{0}', row.data.id);
            componentId = componentId.replace('{componentId}', this.getBaseParams().componentId);
            this.fireEvent('editcomponent', {
                componentClass: col.editComponentClass,
                type: col.editType,
                componentIdSuffix: col.editComponentIdSuffix,
                editComponents: this.contentEditComponents,
                componentId: componentId,
                text: trlVps('Details') //TODO stimmt des?
            });

        }, this);

        this.fireEvent('gotComponentConfigs', this.componentConfigs);

        Vpc.Directories.Item.Directory.Panel.superclass.initComponent.call(this);
    }
});

Ext.reg('vpc.directories.item.directory', Vpc.Directories.Item.Directory.Panel);
