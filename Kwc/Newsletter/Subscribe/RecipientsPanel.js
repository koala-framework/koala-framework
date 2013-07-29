Ext.ns('Kwc.Newsletter.Subscribe');
Kwc.Newsletter.Subscribe.RecipientsPanel = Ext.extend(Kwf.Binding.AbstractPanel, {

    initComponent: function() {
        this.items = [];
        this.layout = 'border';

        Kwc.Newsletter.Subscribe.RecipientsPanel.superclass.initComponent.call(this);
    }
});
Ext.reg('kwc.newsletter.subscribe.recipients', Kwc.Newsletter.Subscribe.RecipientsPanel);
