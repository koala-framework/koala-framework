Ext2.namespace('Kwf.Auto.Grid');

Kwf.Auto.Grid.Window = Ext2.extend(Ext2.Window, {
    layout: 'fit',
    modal: true,
    closeAction: 'hide',
    queryParam: 'id',
    initComponent : function()
    {
        var cfg = Ext2.apply({
            controllerUrl: this.controllerUrl,
            autoLoad: false,
            baseParams: this.baseParams
        }, this.autoGridConfig);
        this.autoGrid = new Kwf.Auto.GridPanel(cfg);
        this.items = [this.autoGrid];

        Kwf.Auto.Grid.Window.superclass.initComponent.call(this);
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

Ext2.reg('kwf.autogridwindow', Kwf.Auto.Grid.Window);
