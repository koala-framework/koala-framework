
Kwf.Enquiries.ViewPanel = Ext2.extend(Kwf.Binding.AbstractPanel,
{
    initComponent: function()
    {
        Kwf.Enquiries.ViewPanel.superclass.initComponent.call(this);
    },

    load: function(params, options) {
        this.getUpdater().update({
            url: this.controllerUrl+'/get-enquiry',
            params: { id: this.getBaseParams().id }
        });
    }
});
