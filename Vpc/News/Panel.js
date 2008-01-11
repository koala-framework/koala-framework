Ext.namespace('Vpc.News');
Vpc.News.Panel = Ext.extend(Vpc.Paragraphs.Panel,
{
    initComponent: function() {
        this.editDialog = new Vps.Auto.Form.Window({
            controllerUrl: '/admin/component/edit/Vpc_News_Form',
            width: 500,
            height: 400,
            formConfig: {
                baseParams: this.baseParams
            }
        });
        Vpc.News.Panel.superclass.initComponent.call(this);
    },
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

        this.editCategories = toolbar.addButton({
            text    : 'Categories',
            handler : function(o, p) {
                var dlg = new Ext.Window({
                    width:  450,
                    height: 370,
                    layout: 'fit',
                    title:  'News Categories',
                    modal:  true,
                    items:  new Vps.Auto.GridPanel({
                        controllerUrl: '/admin/component/edit/Vpc_News_Categories',
                        baseParams: this.getBaseParams()
                    })
                }, this);
                dlg.show();
            },
            scope   : this
        });

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
