
Vps.Enquiries.ViewPanel = Ext.extend(Vps.Binding.AbstractPanel,
{
    initComponent: function()
    {
        Vps.Enquiries.ViewPanel.superclass.initComponent.call(this);
    },

    load: function(params, options) {
        this.getUpdater().update({
            url: this.controllerUrl+'/get-enquiry',
            params: { id: this.getBaseParams().id }
        });
    }
});
