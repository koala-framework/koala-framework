Ext.ns('Kwc.Newsletter.Detail');

Kwc.Newsletter.Detail.RecipientsAction = Ext.extend(Ext.Action, {
    constructor: function(config){
        config = Ext.apply({
        icon    : '/assets/silkicons/database_add.png',
        cls     : 'x-btn-text-icon',
        text    : trlKwf('Add Recipients'),
        tooltip : trlKwf('Adds the currently shown recipients to the newsletter'),
        scope   : this,
        handler : function(a, b, c) {
            if (this.getGrid().getSelectionModel() instanceof Ext.grid.CheckboxSelectionModel) {
                var selectedRows = this.getGrid().getSelectionModel().getSelections();
                var ids = [];
                selectedRows.each(function(selectedRow) { ids.push(selectedRow.id); }, this);
                var params = this.getStore().baseParams;
                params.ids = ids.join(',');
            } else {
                var params = this.getStore().lastOptions.params;
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
        }}, config);
        Kwc.Newsletter.Detail.RecipientsAction.superclass.constructor.call(this, config);
    }
});

Kwc.Newsletter.Detail.RemoveRecipientsAction = Ext.extend(Ext.Action, {
    constructor: function(config){
        config = Ext.apply({
        icon    : '/assets/silkicons/database_delete.png',
        cls     : 'x-btn-text-icon',
        text    : trlKwf('Remove Recipients'),
        tooltip : trlKwf('Removes the currently shown recipients to the newsletter'),
        scope   : this,
        handler : function(a, b, c) {
            if (this.getGrid().getSelectionModel() instanceof Ext.grid.CheckboxSelectionModel) {
                var selectedRows = this.getGrid().getSelectionModel().getSelections();
                var ids = [];
                selectedRows.each(function(selectedRow) { ids.push(selectedRow.id); }, this);
                var params = this.getStore().baseParams;
                params.ids = ids.join(',');
            } else {
                var params = this.getStore().lastOptions.params;
            }
            Ext.Ajax.request({
                url : this.controllerUrl + '/json-remove-recipients',
                params: params,
                success: function(response, options, r) {
                    var msgText = trlKwf('{0} recipients removed, total {1} recipients.', [r.removed, r.after]);
                    Ext.MessageBox.alert(trlKwf('Status'), msgText);
                },
                progress: true,
                timeout: 600000,
                scope: this
            });
        }}, config);
        Kwc.Newsletter.Detail.RecipientsAction.superclass.constructor.call(this, config);
    }
});
