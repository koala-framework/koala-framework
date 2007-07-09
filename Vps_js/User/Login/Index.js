Ext.namespace('Vps.User.Login');

Vps.User.Login.Index = function(renderTo, config)
{
    Ext.apply(this, config);
    dlg = new Vps.User.Login.Dialog(Ext.get(document.body).createChild(), {
        success: function() {
            //reload nach login
            if (config.location != undefined) {
                location.href = config.location;
            } else {
                location.reload();
            }
        },
        scope: this
    });
    dlg.showLogin();
};

Ext.extend(Vps.User.Login.Index, Ext.util.Observable,
{
});

