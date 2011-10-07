Ext.namespace('Vpc.Abstract.List');
Vpc.Abstract.List.FullSizeEditPanel = Ext.extend(Vps.Auto.GridPanel, {
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

        Vpc.Abstract.List.FullSizeEditPanel.superclass.initComponent.call(this);
    }
});

Ext.reg('vpc.listfullsizeeditpanel', Vpc.Abstract.List.FullSizeEditPanel);
