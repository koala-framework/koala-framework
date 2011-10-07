Ext.namespace('Vps.User.Login');

Vps.User.Login.Index = Ext.extend(Ext.Panel,
{
    initComponent: function() {
        Vps.User.Login.Index.superclass.initComponent.call(this);
        dlg = new Vps.User.Login.Dialog({
            message: this.message,
            location: this.location
        });
        dlg.show();
    }
});
