Vps.Form.GridSelect = Ext.extend(Vps.Form.AbstractSelect,
{
    _getWindowItem: function()
    {
        var ret = new Vps.Auto.GridPanel({
            controllerUrl: this.controllerUrl
        });
        ret.on('rowselect', function(selModel,idx,r) {
            this._selectWin.value = {
                id: r.id,
                name: this.displayField ? r.data[this.displayField] : r.id
            };
        }, this);
        return ret;
    }

});
Ext.reg('gridselect', Vps.Form.GridSelect);
