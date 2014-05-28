Ext2.namespace('Kwf.User.Login');

Kwf.User.Login.Index = Ext2.extend(Ext2.Panel,
{
    initComponent: function() {
        Kwf.User.Login.Index.superclass.initComponent.call(this);
        dlg = new Kwf.User.Login.Dialog({
            message: this.message,
            location: this.location
        });
        dlg.show();
    }
});
