Vps.Component.TabPanel = Ext.extend(Ext.TabPanel,
{
    initComponent : function()
    {
        this.deferredRender = false;
        this.items = [];
        for (var i in this.tabs) {
            cls = eval(this.tabs[i]['class']);
            var item = new cls(
                Ext.applyIf(this.tabs[i].config, {
                    autoScroll  : true,
                    closable    : false,
                    title       : i,
                    id          : i
                })
            );
            this.relayEvents(item, ['editcomponent']);
            this.items.add(item);
        }

        Vps.Component.TabPanel.superclass.initComponent.call(this);
    }
});