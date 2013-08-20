
Ext.namespace("Kwf.User.Grid");

Kwf.User.Grid.Grid = Ext.extend(Kwf.Auto.GridPanel,
{
    initComponent: function() {
        Kwf.User.Grid.Grid.superclass.initComponent.call(this);
        if (!this.columnsConfig) this.columnsConfig = { };
        this.columnsConfig['resend_mails'] = {
            clickHandler: function(grid, rowIndex, col, e) {
                var r = grid.getStore().getAt(rowIndex);
                var win = new Kwf.User.Grid.SendMailWindow({
                    controllerUrl: this.controllerUrl,
                    baseParams: {user_id: r.data.id, kwfSessionToken: Kwf.sessionToken}
                });
                win.show();
            },
            scope: this
        };

        this.actions.userdelete = new Ext.Action({
            text    : trlKwf('Delete user'),
            icon    : '/assets/silkicons/delete.png',
            cls     : 'x-btn-text-icon',
            handler : this.onDelete,
            scope: this
        });
        this.actions.userlock = new Ext.Action({
            text    : trlKwf('Lock / unlock user'),
            icon    : '/assets/silkicons/lock.png',
            cls     : 'x-btn-text-icon',
            handler : this.onUnLock,
            scope: this
        });
    },

    onUnLock: function() {
        Ext.Msg.show({
            title: trlKwf('Lock user'),
            msg: trlKwf('Do you really wish to (un)lock this user?'),
            buttons: Ext.Msg.YESNO,
            scope: this,
            fn: function(button) {
                if (button == 'yes') {
                    var selectedRows = this.getGrid().getSelectionModel().getSelections();
                    if (!selectedRows.length) return;

                    var ids = [];
                    var params = this.getBaseParams() || {};
                    var newNewRecords = [];
                    selectedRows.each(function(selectedRow) {
                        ids.push(selectedRow.id);
                    }, this);
                    if (!ids.length) return;

                    params[this.store.reader.meta.id] = ids.join(';');

                    this.el.mask(trlKwf('Locking / unlocking...'));
                    Ext.Ajax.request({
                        url: this.controllerUrl+'/json-user-lock',
                        params: params,
                        success: function(response, options, r) {
                            this.reload();
                            this.fireEvent('datachange', r);
                        },
                        callback: function() {
                            this.el.unmask();
                        },
                        scope : this
                    });
                }
            }
        });
    },

    onDelete : function() {
        Ext.Msg.show({
            title: trlKwf('Delete user'),
            msg: trlKwf('Do you really wish to delete this user?'),
            buttons: Ext.Msg.YESNO,
            scope: this,
            fn: function(button) {
                if (button == 'yes') {
                    var selectedRows = this.getGrid().getSelectionModel().getSelections();
                    if (!selectedRows.length) return;

                    var ids = [];
                    var params = this.getBaseParams() || {};
                    var newNewRecords = [];
                    selectedRows.each(function(selectedRow) {
                        ids.push(selectedRow.id);
                    }, this);
                    if (!ids.length) return;

                    params[this.store.reader.meta.id] = ids.join(';');

                    this.el.mask(trlKwf('Deleting...'));
                    Ext.Ajax.request({
                        url: this.controllerUrl+'/json-user-delete',
                        params: params,
                        success: function(response, options, r) {
                            this.reload();
                            this.fireEvent('deleterow', this.grid);
                            this.fireEvent('datachange', r);

                            this.activeId = null;
                            //wenn gelöscht alle anderen disablen
                            this.bindings.each(function(i) {
                                i.item.disable();
                                i.item.reset();
                            }, this);
                        },
                        callback: function() {
                            this.el.unmask();
                        },
                        scope : this
                    });
                }
            }
        });
    }
});
