Ext2.ns('Kwc.Newsletter.Detail');

Kwc.Newsletter.Detail.RecipientsAction = Ext2.extend(Ext2.Action, {
    constructor: function(config){
        config = Ext2.apply({
        icon    : KWF_BASE_URL+'/assets/silkicons/database_add.png',
        cls     : 'x2-btn-text-icon',
        text    : trlKwf('Add recipients to the queue'),
        tooltip : trlKwf('Adds the currently shown recipients to the newsletter'),
        scope   : this,
        handler : function(a, b, c) {
            if (Ext2.grid.CheckboxSelectionModel && this.getGrid().getSelectionModel() instanceof Ext2.grid.CheckboxSelectionModel) {
                var selectedRows = this.getGrid().getSelectionModel().getSelections();
                var ids = [];
                selectedRows.each(function(selectedRow) { ids.push(selectedRow.id); }, this);
                var params = this.getStore().baseParams;
                params.ids = ids.join(',');
            } else {
                if (this.getStore().lastOptions) {
                    var params = this.getStore().lastOptions.params;
                } else {
                    var params = this.getStore().baseParams;
                }
            }
            Ext2.Ajax.request({
                url : this.controllerUrl + '/json-save-recipients',
                params: params,
                success: function(response, options, r) {
                    var msgText = trlKwf('{0} recipients added, total {1} recipients.', [r.added, r.after]);
                    if (r.rtrExcluded.length) {
                        msgText += '<br /><br />';
                        msgText += trlKwf('The following E-Mail addresses were excluded due to the RTR-ECG-Check (see {0})', ['<a href="http://www.rtr.at/ecg" target="_blank">www.rtr.at/ecg</a>']);
                        msgText += ':<div class="recipientsStatusRtr">'+r.rtrExcluded.join('<br />')+'</div>';
                    }
                    Ext2.MessageBox.alert(trlKwf('Status'), msgText, function() {
                        this.findParentBy(function (container) {
                            if (container instanceof Kwc.Newsletter.Detail.RecipientsPanel) {
                                return true;
                            }
                            return false;
                        }, this).fireEvent('queueChanged');
                    }, this);
                },
                progress: true,
                timeout: 600000,
                scope: this
            });
        }}, config);
        Kwc.Newsletter.Detail.RecipientsAction.superclass.constructor.call(this, config);
    }
});

Kwc.Newsletter.Detail.RemoveRecipientsAction = Ext2.extend(Ext2.Action, {
    constructor: function(config){
        config = Ext2.apply({
        icon    : KWF_BASE_URL+'/assets/silkicons/database_delete.png',
        cls     : 'x2-btn-text-icon',
        text    : trlKwf('Remove recipients from the queue'),
        tooltip : trlKwf('Removes the currently shown recipients from the newsletter'),
        scope   : this,
        handler : function(a, b, c) {
            if (Ext2.grid.CheckboxSelectionModel && this.getGrid().getSelectionModel() instanceof Ext2.grid.CheckboxSelectionModel) {
                var selectedRows = this.getGrid().getSelectionModel().getSelections();
                var ids = [];
                selectedRows.each(function(selectedRow) { ids.push(selectedRow.id); }, this);
                var params = this.getStore().baseParams;
                params.ids = ids.join(',');
            } else {
                if (this.getStore().lastOptions) {
                    var params = this.getStore().lastOptions.params;
                } else {
                    var params = this.getStore().baseParams;
                }
            }
            Ext2.Ajax.request({
                url : this.controllerUrl + '/json-remove-recipients',
                params: params,
                success: function(response, options, r) {
                    var msgText = trlKwf('{0} recipients removed, total {1} recipients.', [r.removed, r.after]);
                    Ext2.MessageBox.alert(trlKwf('Status'), msgText, function() {
                        this.findParentByType('kwc.newsletter.recipients').fireEvent('queueChanged');
                    }, this);
                },
                progress: true,
                timeout: 600000,
                scope: this
            });
        }}, config);
        Kwc.Newsletter.Detail.RecipientsAction.superclass.constructor.call(this, config);
    }
});
