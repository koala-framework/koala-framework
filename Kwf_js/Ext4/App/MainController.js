Ext4.define('Kwf.Ext4.App.MainController', {
    extend: 'Ext.app.Controller',
    $namespace: 'App',
    requires: [
        'Ext.state.LocalStorageProvider',
        'Ext.state.Manager',
        'Kwf.Ext4.Viewport'
    ],
    mainPanel: null,
    onLaunch: function()
    {
        if (!this.mainPanel || !this.mainPanel instanceof Ext4.panel.Panel) {
            throw new Error("mainPanel is required and must be an Ext4.panel.Panel");
        }
        Ext4.create('Kwf.Ext4.Viewport', {
            items: [this.mainPanel]
        });
    },

    init: function()
    {
        if (Ext4.supports.LocalStorage) {
            Ext4.state.Manager.setProvider(new Ext4.state.LocalStorageProvider());
        }
        Ext4.Ajax.disableCaching = false;
        if (!Ext4.Ajax.extraParams) Ext4.Ajax.extraParams = {};
        if (Kwf.sessionToken) Ext4.Ajax.extraParams.kwfSessionToken = Kwf.sessionToken;
        Ext4.Ajax.extraParams.applicationAssetsVersion = Kwf.application.assetsVersion;
        Ext4.Ajax.on('requestexception', this.onAjaxRequestException, this);
    },

    onAjaxRequestException: function(conn, response, options)
    {
        var r = Ext4.decode(response.responseText, true);
        if (response.status == 401) {
            var msg = trlKwf('Please Login');
            if (r && r.role && r.role != 'guest') {
                msg = trlKwf("You don't have enough permissions for this Action");
            }
            Ext4.Msg.alert(trlKwf('Login'), msg, function() {
                location.reload();
            })
        } else if (response.status == 428) {
            var dlg = new Ext4.window.Window({
                autoCreate : true,
                title: trlKwf('Error - wrong version'),
                resizable: false,
                modal: true,
                buttonAlign: 'center',
                bodyPadding: 20,
                plain: true,
                closable: false,
                html: trlKwf('Because of an application update the application has to be reloaded.'),
                buttons: [{
                    text: trlKwf('OK'),
                    handler: function() {
                        location.reload();
                    },
                    scope: this
                }]
            });
            dlg.show();
        } else {
            Ext4.Msg.alert(trlKwf('Error'), trlKwf('A Server failure occured.'));
        }
    }
});
