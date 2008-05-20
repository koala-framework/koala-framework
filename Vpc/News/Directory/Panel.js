Ext.namespace('Vpc.News');
Vpc.News.Panel = Ext.extend(Vps.Auto.GridPanel,
{
    initComponent: function() {
        this.editDialog = new Vps.Auto.Form.Window({
            //TODO: url hier nicht hardkodieren, soll geändert werden können
            controllerUrl: '/admin/component/edit/Vpc_News_Form',
            width: 500,
            height: 400
        });

        if (this.componentPlugins) {
            this.plugins = [ ];
            this.componentPlugins.each(function(v) {
                var pluginClass = eval(v);
                this.plugins.push(new pluginClass());
            }, this);
        }

        this.columnsConfig = {
            edit: {
                clickHandler: function(grid, rowIndex) {
                    var row = grid.getStore().getAt(rowIndex);
                    this.fireEditComponent(row);
                },
                scope: this
            }
        };

        Vpc.News.Panel.superclass.initComponent.call(this);
    },

    fireEditComponent : function(row)
    {
        var bp = this.getBaseParams();
        this.fireEvent('editcomponent', {
            componentClass: this.contentClass,
            componentId: bp.component_id + '_' + row.data.id,
            text: 'Details'
        });
    }
});

Ext.reg('vpc.news', Vpc.News.Panel);
