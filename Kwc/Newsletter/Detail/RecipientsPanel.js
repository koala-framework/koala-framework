Ext2.ns('Kwc.Newsletter.Detail');
Kwc.Newsletter.Detail.RecipientsPanel = Ext2.extend(Kwf.Binding.AbstractPanel, {

    initComponent: function() {
        this.items = [];
        this.layout = 'border';

        this.recipientsPanel = Ext2.ComponentMgr.create(this.recipientsPanel);
        this.on('queueChanged', function() {
            this.recipientsQueuePanel.reload();
            this.mailingPanel.load();
        }, this);
        this.items.push(this.recipientsPanel);

        this.recipientsQueuePanel = Ext2.ComponentMgr.create(this.recipientsQueuePanel);

        this.mailingPanel = Ext2.ComponentMgr.create(this.mailingPanel);

        this.items.push(this.recipientsQueuePanel, this.mailingPanel);
        Kwc.Newsletter.Detail.RecipientsPanel.superclass.initComponent.call(this);
    },

    applyBaseParams : function(baseParams) {
        Kwc.Newsletter.Detail.RecipientsPanel.superclass.applyBaseParams.call(this, baseParams);
        Ext2.apply(this.baseParams, {
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
Ext2.reg('kwc.newsletter.recipients', Kwc.Newsletter.Detail.RecipientsPanel);
