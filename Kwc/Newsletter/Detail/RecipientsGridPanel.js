Ext.ns('Kwc.Newsletter.Detail');
Kwc.Newsletter.Detail.RecipientsGridPanel = Ext.extend(Kwf.Auto.GridPanel, {

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
        Kwc.Newsletter.Detail.RecipientsGridPanel.superclass.initComponent.call(this);
    }
});
Ext.reg('kwc.newsletter.recipients.grid', Kwc.Newsletter.Detail.RecipientsGridPanel);
