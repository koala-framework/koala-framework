Ext.namespace('Kwc.Abstract.List');
Kwc.Abstract.List.FullSizeEditPanel = Ext.extend(Kwf.Auto.GridPanel, {
    initComponent: function() {
        this.addEvents('editcomponent', 'gotComponentConfigs');
        this.fireEvent('gotComponentConfigs', this.componentConfigs);

        this.columnsConfig = {
            edit: {
                clickHandler: function(grid, rowIndex, columnIndex, e) {
                    var row = grid.store.getAt(rowIndex);
                    var data = Kwf.clone(this.editComponents[0]);
                    data.componentId = this.getBaseParams().componentId + '-' + row.get('id');
                    data.editComponents = this.editComponents;
                    this.fireEvent('editcomponent', data);
                },
                scope: this
            }
        };

        Kwc.Abstract.List.FullSizeEditPanel.superclass.initComponent.call(this);
    }
});

Ext.reg('kwc.listfullsizeeditpanel', Kwc.Abstract.List.FullSizeEditPanel);
