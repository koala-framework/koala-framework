Ext.namespace('Vpc.Abstract.List');
Vpc.Abstract.List.Panel = Ext.extend(Ext.Panel,
{
    initComponent: function()
    {
        var cls = eval(this.childConfig['class']);
        this.childPanel = new cls({
            controllerUrl: this.childConfig.config.controllerUrl,
            region: 'center',
            disabled: true
        });

        this.grid = new Vps.Auto.GridPanel({
            controllerUrl: this.controllerUrl,
            baseParams: this.baseParams,
            width: 300,
            split: true,
            region: 'west',
            bindings: [ this.childPanel ]
        });

        this.grid.onAdd = this.onAdd;
        this.layout = 'border';
        this.items = [this.grid, this.childPanel];
        Vpc.Abstract.List.Panel.superclass.initComponent.call(this);
    },

    onAdd : function()
    {
        Ext.Ajax.request({
            mask: true,
            url: this.controllerUrl + '/jsonInsert',
            params: this.getBaseParams(),
            success: function(response, options, r) {
                this.getSelectionModel().clearSelections();
                this.reload({
                    callback: function(o, r, s) {
                        this.getSelectionModel().selectLastRow();
                    },
                    scope: this
                });
            },
            scope: this
        });
    }

});