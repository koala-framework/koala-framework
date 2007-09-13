Ext.namespace('Vps.User.Login');

Vps.User.Login.Index = function(renderTo, config)
{
    dlg = new Vps.User.Login.Dialog(null);
    dlg.showLogin();
};

Ext.extend(Vps.User.Login.Index, Ext.util.Observable,
{
});

