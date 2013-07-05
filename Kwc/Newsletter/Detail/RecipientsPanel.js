Ext.ns('Kwc.Newsletter.Detail');
Kwc.Newsletter.Detail.RecipientsPanel = Ext.extend(Kwf.Binding.AbstractPanel, {

    initComponent: function() {
        this.layout = 'border';

        this.recipientsGridPanel = new Kwc.Newsletter.Detail.RecipientsGridPanel({
            title: trlKwf('Add/Remove Subscriber to Queue'),
            controllerUrl: this.controllerUrl,
            region: 'center'
        });
        this.recipientsGridPanel.on('queueChanged', function() {
            this.recipientsQueuePanel.reload();
        }, this);

        this.recipientsQueuePanel = new Kwc.Newsletter.Detail.RecipientsQueuePanel({
            title: trlKwf('Queue'),
            controllerUrl: this.mailControllerUrl,
            region: 'east',
            width: 500,
            scope: this
        });

        this.mailingPanel = new Kwc.Newsletter.Detail.StartNewsletterPanel({
            title: trlKwf('Mailing'),
            region: 'south',
            controllerUrl: this.mailControllerUrl,
            formControllerUrl: this.mailFormControllerUrl
        });

        this.items = [this.recipientsGridPanel, this.recipientsQueuePanel, this.mailingPanel];
        Kwc.Newsletter.Detail.RecipientsPanel.superclass.initComponent.call(this);
    },

    applyBaseParams : function(baseParams) {
        Kwc.Newsletter.Detail.RecipientsPanel.superclass.applyBaseParams.call(this, baseParams);
        Ext.apply(this.baseParams, {
            newsletterId: this.baseParams.componentId.substr(this.baseParams.componentId.lastIndexOf('_')+1)
        });
        this.recipientsGridPanel.applyBaseParams(this.baseParams);
        this.recipientsQueuePanel.applyBaseParams(this.baseParams);
        this.mailingPanel.applyBaseParams(this.baseParams);
    },

    load: function() {
        this.mailingPanel.load();
    }
});
Ext.reg('kwc.newsletter.recipients', Kwc.Newsletter.Detail.RecipientsPanel);
