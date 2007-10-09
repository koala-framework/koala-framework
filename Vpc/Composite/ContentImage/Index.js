Ext.namespace('Vpc.Composite', 'Vpc.Composite.ContentImage');
Vpc.Composite.ContentImage.Index = Ext.extend(Vps.Component.TabPanel,
{
    initComponent : function()
    {
        Vpc.Composite.ContentImage.Index.superclass.initComponent.call(this);
        this.on('loadtabs', function() {
            this.relayEvents(this.items.items[0], ['editcomponent']);
        });
    }

});