Kwf.Form.GridSelect = Ext2.extend(Kwf.Form.AbstractSelect,
{
    _getWindowItem: function()
    {
        if (!this._windowItem) {
            this._windowItem = new Kwf.Auto.GridPanel({
                controllerUrl: this.controllerUrl
            });
            this._windowItem.on('rowselect', function(selModel,idx,r) {
                this._selectWin.value = {
                    id: r.id,
                    name: this.displayField ? r.data[this.displayField] : r.id
                };
            }, this);
        }
        return this._windowItem;
    }

});
Ext2.reg('gridselect', Kwf.Form.GridSelect);
