Ext.ns('Kwc.Newsletter.Detail');

Kwc.Newsletter.Detail.RecipientsAction = Ext.extend(Ext.Action, {
	constructor: function(config){
		config = Ext.apply({
            icon    : '/assets/silkicons/database_save.png',
            cls     : 'x-btn-text-icon',
            text    : trlKwf('Save Recipients'),
            scope   : this,
            handler : function(a, b, c) {
                if (this.getStore().lastOptions) {
                    var params = this.getStore().lastOptions.params;
                } else {
                    var params = this.getStore().baseParams;
                }
                Ext.Ajax.request({
                    url : this.controllerUrl + '/json-save-recipients',
                    params: params,
                    success: function(response, options, r) {
                        var msgText = trlKwf('{0} recipients added, total {1} recipients.', [r.added, r.after]);
                        if (r.rtrExcluded.length) {
                            msgText += '<br /><br />';
                            msgText += trlKwf('The following E-Mail addresses were excluded due to the RTR-ECG-Check (see {0})', ['<a href="http://www.rtr.at/ecg" target="_blank">www.rtr.at/ecg</a>']);
                            msgText += ':<div class="recipientsStatusRtr">'+r.rtrExcluded.join('<br />')+'</div>';
                        }
                        Ext.MessageBox.alert(trlKwf('Status'), msgText);
                    },
                    progress: true,
                    timeout: 600000,
                    scope: this
                });
            }
        }, config);
		Kwc.Newsletter.Detail.RecipientsAction.superclass.constructor.call(this, config);
	}
});
