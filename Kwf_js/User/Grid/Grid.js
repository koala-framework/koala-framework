
Ext2.namespace("Kwf.User.Grid");

Kwf.User.Grid.Grid = Ext2.extend(Kwf.Auto.GridPanel,
{
    initComponent: function() {
        Kwf.User.Grid.Grid.superclass.initComponent.call(this);
        if (!this.columnsConfig) this.columnsConfig = { };
        this.columnsConfig['resend_mails'] = {
            clickHandler: function(grid, rowIndex, col, e) {
                var r = grid.getStore().getAt(rowIndex);
                var win = new Kwf.User.Grid.SendMailWindow({
                    controllerUrl: this.controllerUrl,
                    baseParams: {user_id: r.data.id}
                });
                win.show();
            },
            scope: this
        };

        this.actions.userdelete = new Ext2.Action({
            text    : trlKwf('Delete user'),
            icon    : '/assets/silkicons/delete.png',
            cls     : 'x2-btn-text-icon',
            handler : this.onDelete,
            scope: this
        });
    },

    onDelete : function() {
        Ext2.Msg.show({
            title: trlKwf('Delete user'),
            msg: trlKwf('Do you really wish to delete this user?'),
            buttons: Ext2.Msg.YESNO,
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
                    Ext2.Ajax.request({
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
