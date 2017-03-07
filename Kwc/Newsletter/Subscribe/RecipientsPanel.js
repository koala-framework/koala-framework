Ext2.ns('Kwc.Newsletter.Subscribe');
Kwc.Newsletter.Subscribe.RecipientsPanel = Ext2.extend(Kwf.Binding.ProxyPanel, {

    initComponent: function() {
        this._logsGrid = new Kwf.Auto.GridPanel({
            controllerUrl: this.logsControllerUrl,
            region: 'south',
            height: 300,
            split: true,
            title: trlKwf('Logs')
        });

        this._subscribersGrid = new Kwf.Auto.GridPanel({
            controllerUrl: this.controllerUrl,
            region: 'center',
            bindings: [{
                item: this._logsGrid, queryParam: 'subscriberId'
            }]
        });
        this._subscribersGrid.on('datachange', function () {
            this._logsGrid.reload();
        }, this);

        if (this.formControllerUrl) {
            this._subscribersGrid.editDialog = {
                controllerUrl: this.formControllerUrl,
                width: 500,
                height: 450
            };
        }

        this.layout = 'border';
        this.region = 'center';
        this.items = [this._subscribersGrid, this._logsGrid];
        this.proxyItem = this._subscribersGrid;

        Kwc.Newsletter.Subscribe.RecipientsPanel.superclass.initComponent.call(this);
    }
});
Ext2.reg('kwc.newsletter.subscribe.recipients', Kwc.Newsletter.Subscribe.RecipientsPanel);
