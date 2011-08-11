Ext.namespace('Vpc.Abstract.List');
Vpc.Abstract.List.List = Ext.extend(Vps.Binding.ProxyPanel,
{
    border: false,
    initComponent: function()
    {
        if (this.childConfig.title) delete this.childConfig.title;
        this.childPanel = Ext.ComponentMgr.create(Ext.applyIf(this.childConfig, {
            region: 'center'
        }));

        this.grid = new Vps.Auto.GridPanel({
            controllerUrl: this.controllerUrl,
            split: true,
            region: 'center',
            baseParams: this.baseParams, //Kompatibilität zu ComponentPanel
            autoLoad: this.autoLoad,
            bindings: [{
                item        : this.childPanel,
                componentIdSuffix: '-{0}'
            }],
            onAdd: this.onAdd
        });
        this.proxyItem = this.grid;

        this.grid.on('datachange', function() {
            this.childPanel.reload();
        }, this);

        var westItems = [this.grid];
        if (this.multiFileUpload) {
            this.multiFileUploadPanel = new Vps.Utils.MultiFileUploadPanel(Ext.applyIf({
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
            westItems.push(this.multiFileUploadPanel);
        }

        this.westPanel = new Ext.Panel({
            layout: 'border',
            region: 'west',
            width: 300,
            border: false,
            items: westItems
        });

        this.layout = 'border';
        this.items = [this.westPanel, this.childPanel];
        Vpc.Abstract.List.List.superclass.initComponent.call(this);
    },

    load: function()
    {
        this.grid.load();
        this.grid.selectId(false);

        this.childPanel.setBaseParams({});
        var f = this.childPanel.getForm();
        if (f) {
            f.clearValues();
            f.clearInvalid();
        }
        this.childPanel.disable();
    },

    onAdd : function()
    {
        Ext.Ajax.request({
            mask: true,
            url: this.controllerUrl + '/json-insert',
            params: this.getBaseParams(),
            success: function(response, options, r) {
                this.getSelectionModel().clearSelections();
                this.reload({
                    callback: function(o, r, s) {
                        this.getSelectionModel().selectLastRow();
                    },
                    scope: this
                });
            },
            scope: this
        });
    }
});
Ext.reg('vpc.list.list', Vpc.Abstract.List.List);
