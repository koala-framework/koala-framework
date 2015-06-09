Kwc.Directories.Item.Directory.GridPanel = Ext2.extend(Kwf.Binding.ProxyPanel,
{
    layout: 'border',

    initComponent: function() {
        this.grid = new Kwf.Auto.GridPanel({
            controllerUrl: this.controllerUrl,
            multiFileUpload: this.multiFileUpload,
            region: 'center',
            baseParams: this.baseParams
        });
        this.proxyItem = this.grid;
        this.items = [this.grid];

        // Add multiFileUpload field if set
        if (this.multiFileUpload) {
            this.multiFileUploadPanel = new Kwf.Utils.MultiFileUploadPanel(Ext2.applyIf({
                border: false,
                region: 'south',
                height: 50,
                bodyStyle: 'padding-top: 15px; padding-left:80px;',
                controllerUrl: this.controllerUrl,
                baseParams: this.baseParams
            }), this.multiFileUpload);
            this.multiFileUploadPanel.on('uploaded', function() {
                this.grid.reload();
            }, this);
            this.items.push(this.multiFileUploadPanel);
        }

        Kwc.Directories.Item.Directory.GridPanel.superclass.initComponent.call(this);
    },

    applyBaseParams: function(baseParams) {
        if (this.multiFileUploadPanel) {
            this.multiFileUploadPanel.applyBaseParams(baseParams);
        }
        return Kwc.Directories.Item.Directory.GridPanel.superclass.applyBaseParams.apply(this, arguments);
    },
    setBaseParams : function(baseParams) {
        if (this.multiFileUploadPanel) {
            this.multiFileUploadPanel.setBaseParams(baseParams);
        }
        return Kwc.Directories.Item.Directory.GridPanel.superclass.setBaseParams.apply(this, arguments);
    },
    addBinding: function (bindForm) {
        this.proxyItem.addBinding(bindForm);
    }
});
