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

        if (this.componentPlugins) {
            this.plugins = [ ];
            this.componentPlugins.each(function(v) {
                var pluginClass = eval(v);
                this.plugins.push(new pluginClass());
            }, this);
        }

        Vpc.News.Panel.superclass.initComponent.call(this);
    },

    addButtons : function()
    {
        var toolbar = this.grid.getTopToolbar();

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
            componentId: this.baseParams.component_id + '_' + row.data.id,
            text: 'Details'
        });
    },
    
    onAdd : function()
    {
        this.applyBaseParams({
            componentId: this.baseParams.component_id
        });
        Vpc.News.Panel.superclass.onAdd.call(this);
    }
});
