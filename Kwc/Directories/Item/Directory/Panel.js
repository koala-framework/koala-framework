Ext.namespace('Kwc.Directories.Item.Directory');
Kwc.Directories.Item.Directory.Panel = Ext.extend(Kwf.Auto.GridPanel,
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
                text: trlKwf('Details') //TODO stimmt des?
            });

        }, this);

        this.fireEvent('gotComponentConfigs', this.componentConfigs);

        Kwc.Directories.Item.Directory.Panel.superclass.initComponent.call(this);
    }
});

Ext.reg('kwc.directories.item.directory', Kwc.Directories.Item.Directory.Panel);
