Ext.ns('Vpc.Newsletter.Detail');
Vpc.Newsletter.Detail.RecipientsPanel = Ext.extend(Vps.Auto.GridPanel, {

    initComponent: function() {
        this.actions.saveRecipients = new Vpc.Newsletter.Detail.RecipientsAction({scope: this});
        if (this.formControllerUrl) {
            this.editDialog = {
                controllerUrl: this.formControllerUrl,
                width: 500,
                height: 450
            };
        }
        Vpc.Newsletter.Detail.RecipientsPanel.superclass.initComponent.call(this);
    }
});
Ext.reg('vpc.newsletter.recipients', Vpc.Newsletter.Detail.RecipientsPanel);
