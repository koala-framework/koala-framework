Kwf.Auto.ImportPanel = Ext2.extend(Kwf.Auto.FormPanel,
{
    initComponent : function()
    {
        Kwf.Auto.ImportPanel.superclass.initComponent.call(this);
        this.on('datachange', function(r) {
            this.onAdd();
            var msg = trlKwf('The File has been imported successfully.');
            if (r.message) msg = r.message;
            Ext2.MessageBox.show({
                title    : trlKwf('Import done'),
                msg      : msg,
                width    : 400,
                buttons  : Ext2.MessageBox.OK
            });
        }, this);
    }
});
Ext2.reg('kwf.import', Kwf.Auto.ImportPanel);
