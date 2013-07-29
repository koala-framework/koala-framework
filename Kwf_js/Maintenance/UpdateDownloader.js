Ext.ns('Kwf.Maintenance');
Kwf.Maintenance.UpdateDownloader = Ext.extend(Ext.Panel, {
    border: false,
    initComponent: function() {
        this.libraryUrl = new Ext.form.TextField({
            name: 'libraryUrl',
            fieldLabel: 'Library Url',
            value: this.defaultLibraryUrl,
            width: 600
        });
        this.kwfUrl = new Ext.form.TextField({
            name: 'kwfUrl',
            fieldLabel: 'Kwf Url',
            value: this.defaultKwfUrl,
            width: 600
        });
        this.appUrl = new Ext.form.TextField({
            name: 'appUrl',
            fieldLabel: 'App Url',
            value: this.defaultAppUrl,
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
                        //after download finished execute updates
                        Kwf.Utils.BackgroundProcess.request({
                            url: '/kwf/maintenance/update-downloader/json-execute-updates',
                            progress: true,
                            scope: this,
                            success: function(response, options, r) {
                                if (r.errMsg) {
                                    Ext.Msg.alert(trlKwf('Update Error'), r.message+"<br />"+r.errMsg.replace("\n", "<br />"));
                                } else if (r.message) {
                                    Ext.Msg.alert(trlKwf('Updates Finished'), r.message);
                                }
                            }
                        });
                    }
                });
            },
            scope: this
        }];
        Kwf.Maintenance.UpdateDownloader.superclass.initComponent.call(this);
    }
});
Ext.reg('kwf.maintenance.updateDownloader', Kwf.Maintenance.UpdateDownloader);
