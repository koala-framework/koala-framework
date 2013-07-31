Ext.ns('Kwc.Newsletter.Subscribe');
Kwc.Newsletter.Subscribe.RecipientsPanel = Ext.extend(Kwf.Auto.GridPanel, {

    initComponent: function() {
        if (this.formControllerUrl) {
            this.editDialog = {
                controllerUrl: this.formControllerUrl,
                width: 500,
                height: 450
            };
        }

        Kwc.Newsletter.Subscribe.RecipientsPanel.superclass.initComponent.call(this);
    }
});
Ext.reg('kwc.newsletter.subscribe.recipients', Kwc.Newsletter.Subscribe.RecipientsPanel);
