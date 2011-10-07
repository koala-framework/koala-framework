Ext.ns('Kwc.Newsletter.Detail');
Kwc.Newsletter.Detail.RecipientsPanel = Ext.extend(Kwf.Auto.GridPanel, {

    initComponent: function() {
        this.actions.saveRecipients = new Kwc.Newsletter.Detail.RecipientsAction({scope: this});
        if (this.formControllerUrl) {
            this.editDialog = {
                controllerUrl: this.formControllerUrl,
                width: 500,
                height: 450
            };
        }
        Kwc.Newsletter.Detail.RecipientsPanel.superclass.initComponent.call(this);
    }
});
Ext.reg('kwc.newsletter.recipients', Kwc.Newsletter.Detail.RecipientsPanel);
