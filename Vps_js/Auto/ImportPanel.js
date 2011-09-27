Vps.Auto.ImportPanel = Ext.extend(Vps.Auto.FormPanel,
{
    initComponent : function()
    {
        Vps.Auto.ImportPanel.superclass.initComponent.call(this);
        this.on('datachange', function(r) {
            this.onAdd();
            var msg = trlVps('The File has been imported successfully.');
            if (r.message) msg = r.message;
            Ext.MessageBox.show({
                title    : trlVps('Import done'),
                msg      : msg,
                width    : 400,
                buttons  : Ext.MessageBox.OK
            });
        }, this);
    }
});
Ext.reg('vps.import', Vps.Auto.ImportPanel);
