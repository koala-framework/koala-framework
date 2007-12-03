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
                    this.fireEvent('editcomponent', {
                        componentClass: this.contentClass,
                        pageId: this.baseParams.page_id, 
                        componentKey: this.baseParams.component_key + '_' + row.data.id, 
                        text: 'Details'
                    });
                }
            },
            scope   : this
        });
    }
});
