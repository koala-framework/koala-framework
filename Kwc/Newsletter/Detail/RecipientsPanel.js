Ext.ns('Kwc.Newsletter.Detail');
Kwc.Newsletter.Detail.RecipientsPanel = Ext.extend(Kwf.Auto.GridPanel, {

    initComponent: function() {
        this.actions.saveRecipients = new Kwc.Newsletter.Detail.RecipientsAction({scope: this});
        this.actions.removeRecipients = new Kwc.Newsletter.Detail.RemoveRecipientsAction({scope: this});
        if (this.formControllerUrl) {
            this.editDialog = {
                controllerUrl: this.formControllerUrl,
                width: 500,
                height: 450
            };
        }
                                                                            //cut off _123 for detail
//         this.baseParams.newsletterComponentId = this.baseParams.componentId.substr(0, this.baseParams.componentId.lastIndexOf("_"));
//         delete this.baseParams.componentId;
        Kwc.Newsletter.Detail.RecipientsPanel.superclass.initComponent.call(this);
    }
});
Ext.reg('kwc.newsletter.recipients', Kwc.Newsletter.Detail.RecipientsPanel);
