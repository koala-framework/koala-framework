Ext.namespace('Vpc.Columns.Trl');
Vpc.Columns.Trl.Panel = Ext.extend(Vps.Auto.GridPanel, {
    initComponent: function() {
        this.addEvents('editcomponent', 'gotComponentConfigs');
        this.fireEvent('gotComponentConfigs', this.componentConfigs);

        this.columnsConfig = {
            edit: {
                clickHandler: function(grid, rowIndex, columnIndex, e) {
                    var row = grid.store.getAt(rowIndex);
                    var data = Vps.clone(this.editComponents[0]);
                    data.componentId = this.getBaseParams().componentId + '-' + row.get('id');
                    data.editComponents = this.editComponents;
                    this.fireEvent('editcomponent', data);
                },
                scope: this
            }
        };

        Vpc.Columns.Trl.Panel.superclass.initComponent.call(this);
    }
});

Ext.reg('vpc.columns.trl', Vpc.Columns.Trl.Panel);
