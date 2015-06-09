Ext2.ns('Kwc.Newsletter.Detail');
Kwc.Newsletter.Detail.RecipientsQueuePanel = Ext2.extend(Kwf.Auto.GridPanel, {

    initComponent: function() {
        this.actions.deleteAll = new Ext2.Action({
            text: trlKwf('Delete All'),
            icon: '/assets/silkicons/bin_empty.png',
            cls: 'x2-btn-text-icon',
            handler: function(){
                Ext2.Msg.confirm(
                    trlKwf('Are you sure?'),
                    trlKwf('Do you really want to delete all receivers with status "queued"?'),
                    function(result) {
                        if (result == 'yes') {
                            Ext2.Ajax.request({
                                url : this.controllerUrl + '/json-delete-all',
                                params: this.getBaseParams(),
                                success: function(response, options, r) {
                                    Ext2.MessageBox.alert(trlKwf('Status'), r.message);
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
Ext2.reg('kwc.newsletter.recipients.queue', Kwc.Newsletter.Detail.RecipientsQueuePanel);
