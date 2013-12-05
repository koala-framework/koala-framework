Ext4.define('Kwf.Ext4.App.MainController', {
    extend: 'Ext.app.Controller',
    requires: [
        'Ext.state.LocalStorageProvider',
        'Ext.state.Manager',
        'Kwf.Ext4.Viewport'
    ],
    mainPanel: null,
    onLaunch: function()
    {
        if (Ext4.supports.LocalStorage) {
            Ext4.state.Manager.setProvider(new Ext4.state.LocalStorageProvider());
        }
        Ext4.Ajax.disableCaching = false;
        if (!Ext4.Ajax.extraParams) Ext4.Ajax.extraParams = {};
        if (Kwf.sessionToken) Ext4.Ajax.extraParams.kwfSessionToken = Kwf.sessionToken;
        Ext4.Ajax.extraParams.applicationAssetsVersion = Kwf.application.assetsVersion;

        if (!this.mainPanel || !this.mainPanel instanceof Ext4.panel.Panel) {
            throw new Error("mainPanel is required and must be an Ext4.panel.Panel");
        }
        Ext4.create('Kwf.Ext4.Viewport', {
            items: [this.mainPanel]
        });
    }
});
