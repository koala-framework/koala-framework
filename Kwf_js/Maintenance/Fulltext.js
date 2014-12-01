Ext2.ns('Kwf.Maintenance');
Kwf.Maintenance.Fulltext = Ext2.extend(Ext2.Panel, {
    border: false,
    initComponent: function() {
        this.buttons = [{
            text: trlKwf('Rebuild'),
            handler: function() {
                Kwf.Utils.BackgroundProcess.request({
                    url: '/kwf/maintenance/fulltext/json-rebuild',
                    progress: true,
                    scope: this,
                    success: function(response, options, r) {
                        if (r.errMsg) {
                            Ext2.Msg.alert(trlKwf('Error'), r.message+"<br />"+r.errMsg.replace("\n", "<br />"));
                        } else if (r.message) {
                            Ext2.Msg.alert(trlKwf('Finished'), r.message);
                        }
                    }
                });
            },
            scope: this
        }];
        Kwf.Maintenance.Fulltext.superclass.initComponent.call(this);
    }
});
Ext2.reg('kwf.maintenance.fulltext', Kwf.Maintenance.Fulltext);
