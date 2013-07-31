Ext.ns('Kwc.Newsletter.Detail');
Kwc.Newsletter.Detail.RecipientsQueuePanel = Ext.extend(Kwf.Auto.GridPanel, {

    initComponent: function() {
        this.actions.deleteAll = new Ext.Action({
            text: trlKwf('Delete All'),
            icon: '/assets/silkicons/bin_empty.png',
            cls: 'x-btn-text-icon',
            handler: function(){
                Ext.Msg.confirm(
                    trlKwf('Are you sure?'),
                    trlKwf('Do you really want to delete all receivers with status "queued"?'),
                    function(result) {
                        if (result == 'yes') {
                            Ext.Ajax.request({
                                url : this.controllerUrl + '/json-delete-all',
                                params: this.getBaseParams(),
                                success: function(response, options, r) {
                                    Ext.MessageBox.alert(trlKwf('Status'), r.message);
                                    this.reload();
                                },
                                scope: this
                            });
                        }
                    },
                    this
                );
            },
            scope: this
        });
        Kwc.Newsletter.Detail.RecipientsQueuePanel.superclass.initComponent.call(this);
    }
});
Ext.reg('kwc.newsletter.recipients.queue', Kwc.Newsletter.Detail.RecipientsQueuePanel);
