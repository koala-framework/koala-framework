Ext.namespace('Vpc.ListChildPages.Teaser');
Vpc.ListChildPages.Teaser.Panel = Ext.extend(Vps.Binding.ProxyPanel,
{
    initComponent: function()
    {
        this.childPanel = Ext.ComponentMgr.create(Ext.applyIf(this.childConfig, {
            region: 'center'
        }));

        this.grid = new Vps.Auto.GridPanel({
            controllerUrl: this.controllerUrl,
            width: 300,
            split: true,
            region: 'west',
            baseParams: this.baseParams, //Kompatibilität zu ComponentPanel
            autoLoad: this.autoLoad,
            bindings: [{
                item        : this.childPanel,
                componentIdSuffix: '-{0}'
            }]
        });
        this.proxyItem = this.grid;

        this.layout = 'border';
        this.items = [this.grid, this.childPanel];
        Vpc.Abstract.List.Panel.superclass.initComponent.call(this);
    },

    load: function()
    {
        this.grid.load();
        var f = this.childPanel.getForm();
        if (f) {
            f.clearValues();
        }
        this.childPanel.disable();
    }
});
Ext.reg('vpc.listchildpages', Vpc.ListChildPages.Teaser.Panel);
