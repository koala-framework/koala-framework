Ext.namespace('Vpc.Abstract.List');
Vpc.Abstract.List.Panel = Ext.extend(Vps.Auto.AbstractPanel,
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
            width: 300,
            split: true,
            region: 'west',
            baseParams: this.baseParams, //Kompatibilität zu ComponentPanel
            bindings: [{
                item        : this.childPanel,
                componentIdSuffix: '-{0}'
            }]
        });

        this.grid.onAdd = this.onAdd;
        this.layout = 'border';
        this.items = [this.grid, this.childPanel];
        Vpc.Abstract.List.Panel.superclass.initComponent.call(this);
    },

    load: function()
    {
        this.grid.applyBaseParams({
            component_id: this.getBaseParams()['component_id']
        });
        this.grid.load();
        this.childPanel.getForm().clearValues();
        this.childPanel.disable();
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
    },
    setBaseParams : function(baseParams) {
        this.grid.setBaseParams(baseParams);
    },
    applyBaseParams : function(baseParams) {
        this.grid.applyBaseParams(baseParams);
    },
    getBaseParams : function() {
        return this.grid.getBaseParams();
    }
});