Ext.ns('Kwf.Maintenance');
Kwf.Maintenance.UpdateDownloader = Ext.extend(Ext.Panel, {
    border: false,
    initComponent: function() {
        this.libraryUrl = new Ext.form.TextField({
            name: 'libraryUrl',
            fieldLabel: 'Library Url',
            value: 'https://github.com/vivid-planet/library/archive/master.tar.gz',
            width: 600
        });
        this.kwfUrl = new Ext.form.TextField({
            name: 'kwfUrl',
            fieldLabel: 'Kwf Url',
            value: 'https://github.com/vivid-planet/koala-framework/archive/3.3-installer.tar.gz',
            width: 600
        });
        this.appUrl = new Ext.form.TextField({
            name: 'appUrl',
            fieldLabel: 'App Url',
            value: 'https://github.com/vivid-planet/kwf-cms-demo/archive/master.tar.gz',
            width: 600
        });
        this.layout = 'form',
        this.items = [
            this.libraryUrl,
            this.kwfUrl,
            this.appUrl
        ];
        this.buttons = [{
            text: trlKwf('Download Updates'),
            handler: function() {
                Kwf.Utils.BackgroundProcess.request({
                    url: '/kwf/maintenance/update-downloader/json-download-updates',
                    params: {
                        libraryUrl: this.libraryUrl.getValue(),
                        kwfUrl: this.kwfUrl.getValue(),
                        appUrl: this.appUrl.getValue()
                    },
                    progress: true,
                    scope: this,
                    timeout: 10*60*1000,
                    success: function() {
                        alert('Download Finished, execute updates now');
                        location.href = '/kwf/maintenance/update';
                    }
                });
            },
            scope: this
        }];
        Kwf.Maintenance.UpdateDownloader.superclass.initComponent.call(this);
    }
});
Ext.reg('kwf.maintenance.updateDownloader', Kwf.Maintenance.UpdateDownloader);
