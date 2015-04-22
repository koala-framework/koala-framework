Ext2.namespace('Kwc.Articles.Directory');
Kwc.Articles.Directory.TabsPanel = Ext2.extend(Kwc.Directories.Item.Directory.TabsPanel,
{
    actionText: '',

    initComponent: function()
    {
        Kwc.Articles.Directory.TabsPanel.superclass.initComponent.call(this);
        this.grid.on('rendergrid', function() {
            var filter = this.grid.getFilter('deleted');
            var action = this.grid.getAction('delete');
            action.setHandler(this.onDelete, this.grid);
            filter.on('filter', function(el, val) {
                if (val.query_deleted == 1) {
                    this.actionText = action.getText();
                    action.setText('Wiederherstellen');
                } else {
                    action.setText(this.actionText);
                }
            }, this);
        }, this);
    },

    onDelete: function()
    {
        if (this.getFilter('deleted').getParams('deleted').deleted == 1) {
            Ext2.Msg.show({
                title: trlKwf('Restore'),
                msg: trlKwf('Do you really want to restore this entry (entries)?'),
                buttons: Ext2.Msg.YESNO,
                scope: this,
                fn: function(button) {
                    if (button == 'yes') {
                        var selectedRows = this.getGrid().getSelectionModel().getSelections();
                        if (!selectedRows.length) return;

                        var ids = [];
                        var params = this.getBaseParams() || {};
                        var newNewRecords = [];
                        selectedRows.each(function(selectedRow)
                        {
                            if (selectedRow.data.id == 0) {
                                this.store.remove(selectedRow);
                                this.store.newRecords.each(function(r) {
                                    if (selectedRow != r) {
                                        newNewRecords.push(r);
                                    }
                                });
                            } else {
                                ids.push(selectedRow.id);
                            }
                        }, this);
                        this.store.newRecords = newNewRecords;
                        if (!ids.length) return;

                        params[this.store.reader.meta.id] = ids.join(';');

                        this.el.mask(trlKwf('Restoring...'));
                        Ext2.Ajax.request({
                            url: this.controllerUrl+'/json-restore',
                            params: params,
                            success: function(response, options, r) {
                                this.activeId = null;
                                //wenn gelöscht alle anderen disablen
                                this.bindings.each(function(i) {
                                    i.item.disable();
                                    i.item.reset();
                                }, this);

                                this.reload();
                                this.fireEvent('deleterow', this.grid);
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
        } else {
            Kwf.Auto.GridPanel.prototype.onDelete.call(this);
        }
    }
});

Ext2.reg('kwc.articles.directory.tabs', Kwc.Articles.Directory.TabsPanel);
