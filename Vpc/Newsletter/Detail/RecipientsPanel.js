Ext.ns('Vpc.Newsletter.Detail');
Vpc.Newsletter.Detail.RecipientsPanel = Ext.extend(Vps.Auto.GridPanel, {
	
	initComponent: function() {
	    this.actions.saveRecipients = new Ext.Action({
            icon    : '/assets/silkicons/database_save.png',
            cls     : 'x-btn-text-icon',
            text    : trlVps('Save Recipients'),
            scope   : this,
            handler : function(a, b, c) {
	    		var params = this.initialConfig.baseParams;
	    		if (this.getStore().lastOptions) {
	    			params = this.getStore().lastOptions.params;
	    		}
	    		Ext.Ajax.request({
                    url : this.controllerUrl + '/json-save-recipients',
                    params: params,
                    success: function(response, options, r) {
                        Ext.MessageBox.alert(
                            trlVps('Status'),
                            trlVps('{0} recipients added, total {1} recipients.', [r.added, r.after])
                        );
                    },
                    progress: true,
                    timeout: 600000,
                    scope: this
                });
            }
        });
		Vpc.Newsletter.Detail.RecipientsPanel.superclass.initComponent.call(this);
    },
    
    getKeys: function() {
    	return this.store.data.keys;
    }
});
Ext.reg('vpc.newsletter.recipients', Vpc.Newsletter.Detail.RecipientsPanel);
