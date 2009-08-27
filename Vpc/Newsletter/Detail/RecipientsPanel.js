Ext.ns('Vpc.Newsletter.Detail');
Vpc.Newsletter.Detail.RecipientsPanel = Ext.extend(Vps.Auto.GridPanel, {

    initComponent: function() {
		this.actions.saveRecipients = new Vpc.Newsletter.Detail.RecipientsAction({scope: this});
        Vpc.Newsletter.Detail.RecipientsPanel.superclass.initComponent.call(this);
    }
});
Ext.reg('vpc.newsletter.recipients', Vpc.Newsletter.Detail.RecipientsPanel);
