Vps.Login.Index = function(renderTo, config)
{
    Ext.apply(this, config);

    dlg = new Vps.Login.Dialog(Ext.get(document.body).createChild(), {
        success: function() {
            //reload nach login
            location.reload();
        },
        scope: this
    });
    dlg.showLogin();
};

Ext.extend(Vps.Login.Index, Ext.util.Observable,
{
});

