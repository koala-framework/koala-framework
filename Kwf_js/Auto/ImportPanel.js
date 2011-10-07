Kwf.Auto.ImportPanel = Ext.extend(Kwf.Auto.FormPanel,
{
    initComponent : function()
    {
        Kwf.Auto.ImportPanel.superclass.initComponent.call(this);
        this.on('datachange', function(r) {
            this.onAdd();
            var msg = trlKwf('The File has been imported successfully.');
            if (r.message) msg = r.message;
            Ext.MessageBox.show({
                title    : trlKwf('Import done'),
                msg      : msg,
                width    : 400,
                buttons  : Ext.MessageBox.OK
            });
        }, this);
    }
});
Ext.reg('kwf.import', Kwf.Auto.ImportPanel);
