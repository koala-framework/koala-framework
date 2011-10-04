Vpc.TableXlsImportForm = Ext.extend(Vps.Auto.FormPanel,
{
    initComponent : function()
    {
        Vpc.TableXlsImportForm.superclass.initComponent.call(this);
        this.on('datachange', function() {
            this.onAdd();
            Ext.MessageBox.show({
                title    : trlVps('Import done'),
                msg      : trlVps('The File has been imported successfully.'),
                width    : 400,
                buttons  : Ext.MessageBox.OK
            });
        }, this);
    }
});
Ext.reg('vpc.tablexlsimport', Vpc.TableXlsImportForm);
