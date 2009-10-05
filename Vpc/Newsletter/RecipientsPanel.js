Ext.ns('Vpc.Newsletter');
Vpc.Newsletter.RecipientsPanel = Ext.extend(Vps.Auto.GridPanel, {

    initComponent: function() {
        this.actions.saveRecipients = new Vpc.Newsletter.RecipientsAction({scope: this});
        if (this.formControllerUrl) {
            this.editDialog = {
                controllerUrl: this.formControllerUrl,
                width: 500,
                height: 450
            };
        }
        Vpc.Newsletter.RecipientsPanel.superclass.initComponent.call(this);
    }
});
Ext.reg('vpc.newsletter.recipients', Vpc.Newsletter.RecipientsPanel);
