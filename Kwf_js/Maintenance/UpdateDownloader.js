Ext2.ns('Kwf.Maintenance');
Kwf.Maintenance.UpdateDownloader = Ext2.extend(Ext2.Panel, {
    border: false,
    initComponent: function() {
        this.libraryUrl = new Ext2.form.TextField({
            name: 'libraryUrl',
            fieldLabel: 'Library Url',
            value: this.defaultLibraryUrl,
            width: 600
        });
        this.kwfUrl = new Ext2.form.TextField({
            name: 'kwfUrl',
            fieldLabel: 'Kwf Url',
            value: this.defaultKwfUrl,
            width: 600
        });
        this.appUrl = new Ext2.form.TextField({
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
                Kwf.application.assetsVersion = null; //unset to avoid checking (which is problematic during update)
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
                            timeout: 10*60*1000,
                            success: function(response, options, r) {
                                if (r.errMsg) {
                                    Ext2.Msg.alert(trlKwf('Update Error'), r.message+"<br />"+r.errMsg.replace("\n", "<br />"));
                                } else if (r.message) {
                                    Ext2.Msg.alert(trlKwf('Updates Finished'), r.message, function() {
                                        location.href = '/kwf/welcome';
                                    }, scope);
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
Ext2.reg('kwf.maintenance.updateDownloader', Kwf.Maintenance.UpdateDownloader);
