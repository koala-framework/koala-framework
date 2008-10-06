//TODO merge with news panel
Ext.namespace('Vpc.Products.Directory');
Vpc.Products.Directory.Panel = Ext.extend(Vps.Auto.GridPanel,
{
    initComponent: function() {
        this.columnsConfig = {
            edit: {
                clickHandler: function(grid, rowIndex) {
                    var row = grid.getStore().getAt(rowIndex);
                    this.fireEditComponent(row);
                },
                scope: this
            }
        };

        Vpc.Products.Directory.Panel.superclass.initComponent.call(this);
    },

    fireEditComponent : function(row)
    {
        this.fireEvent('editcomponent', {
            componentClass: this.contentClass,
            componentId: this.idTemplate.replace('{0}', row.data.id),
            text: 'Details'
        });
    }
});

Ext.reg('vpc.shop.products', Vpc.Products.Directory.Panel);
