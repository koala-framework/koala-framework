Ext.ns('Kwc.Newsletter.Detail');
Kwc.Newsletter.Detail.RecipientsPanel = Ext.extend(Kwf.Binding.AbstractPanel, {

    initComponent: function() {
        this.items = [];
        this.layout = 'border';

        this.recipientsPanel = Ext.ComponentMgr.create(this.recipientsPanel);
        this.on('queueChanged', function() {
            this.recipientsQueuePanel.reload();
            this.mailingPanel.load();
        }, this);
        this.items.push(this.recipientsPanel);

        this.recipientsQueuePanel = Ext.ComponentMgr.create(this.recipientsQueuePanel);

        this.mailingPanel = Ext.ComponentMgr.create(this.mailingPanel);

        this.items.push(this.recipientsQueuePanel, this.mailingPanel);
        Kwc.Newsletter.Detail.RecipientsPanel.superclass.initComponent.call(this);
    },

    applyBaseParams : function(baseParams) {
        Kwc.Newsletter.Detail.RecipientsPanel.superclass.applyBaseParams.call(this, baseParams);
        Ext.apply(this.baseParams, {
            newsletterId: this.baseParams.componentId.substr(this.baseParams.componentId.lastIndexOf('_')+1)
        });
        this.recipientsPanel.applyBaseParams(this.baseParams);
        this.recipientsQueuePanel.applyBaseParams(this.baseParams);
        this.mailingPanel.applyBaseParams(this.baseParams);
    },

    load: function() {
        this.recipientsPanel.load();
        this.recipientsQueuePanel.load();
        this.mailingPanel.load();
    }
});
Ext.reg('kwc.newsletter.recipients', Kwc.Newsletter.Detail.RecipientsPanel);
