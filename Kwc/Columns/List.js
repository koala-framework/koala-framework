Ext.namespace('Kwc.Columns');
Kwc.Columns.List = Ext.extend(Kwc.Abstract.List.List,
{
    initComponent: function()
    {
        Kwc.Columns.List.superclass.initComponent.call(this);
        var deleteAction = this.grid.getAction('delete');
        deleteAction.disable();
        deleteAction.initialConfig.needsSelection = false;
        this.grid.on('selectionchange', function(selModel) {
            if (selModel.getSelected()) {
                if (this.grid.store.totalLength <= selModel.getSelected().get('total_columns')) {
                    deleteAction.disable();
                } else {
                    deleteAction.enable();
                }
            }
        }, this);
    }
});
Ext.reg('kwc.columns.list', Kwc.Columns.List);
