Ext.namespace('Vpc.News');
Vpc.News.Panel = Ext.extend(Vpc.Paragraphs.Panel,
{
    addButtons : function()
    {
        var toolbar = this.grid.getTopToolbar();
        toolbar.addSeparator();
        this.editButton = toolbar.addButton({
            text    : 'Edit',
            handler : function(o, p) {
                var row = this.grid.getSelectionModel().getSelected();
                if (row != undefined) {
                    this.fireEditComponent(row);
                }
            },
            scope   : this
        });
        // Event-Handler für Edit-Dialog rausschmeißen
        this.getGrid().getColumnModel().getColumnByDataIndex('edit').clickHandler = function() {};
        this.getGrid().on('cellclick', function(grid, rowIndex, columnIndex) {
            if (columnIndex == 2) {
                var row = grid.getStore().getAt(rowIndex);
                this.fireEditComponent(row);
            }
        }, this);
    },
    
    fireEditComponent : function(row)
    {
        this.fireEvent('editcomponent', {
            componentClass: this.contentClass,
            pageId: this.baseParams.page_id, 
            componentKey: this.baseParams.component_key + '_' + row.data.id, 
            text: 'Details'
        });
    },
    
    onAdd : function()
    {
        this.applyBaseParams({
            pageId: this.baseParams.page_id, 
            componentKey: this.baseParams.component_key 
        });
        Vpc.News.Panel.superclass.onAdd.call(this);
    }
});
