Ext.namespace('Vps.Auto.Grid');

Vps.Auto.Grid.Window = Ext.extend(Ext.Window, {
    layout: 'fit',
    modal: true,
    closeAction: 'hide',
    queryParam: 'id',
    initComponent : function()
    {
        var cfg = Ext.apply({
            controllerUrl: this.controllerUrl,
            autoLoad: false,
            baseParams: this.baseParams
        }, this.autoGridConfig);
        this.autoGrid = new Vps.Auto.GridPanel(cfg);
        this.items = [this.autoGrid];

        Vps.Auto.Grid.Window.superclass.initComponent.call(this);
    },

    showEdit: function(id, record) {
        var p = {};
        p[this.queryParam] = id;
        this.applyBaseParams(p);
        this.show();
        this.autoGrid.load();
    },

    getAutoGrid : function()
    {
        return this.autoGrid;
    },

    getGrid : function()
    {
        return this.getAutoGrid().getGrid();
    },

    getBaseParams: function()
    {
        return this.getAutoGrid().getBaseParams();
    },
    applyBaseParams: function(p)
    {
        this.getAutoGrid().applyBaseParams(p);
    }
});

Ext.reg('vps.autogridwindow', Vps.Auto.Grid.Window);
