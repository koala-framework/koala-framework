Ext.namespace('Vpc.Abstract.List');
Vpc.Abstract.List.List = Ext.extend(Vps.Binding.ProxyPanel,
{
    initComponent: function()
    {
        this.layout = 'border';

        var gridConfig = {
            controllerUrl: this.controllerUrl,
            region: 'center',
            split: true,
            baseParams: this.baseParams, //Kompatibilität zu ComponentPanel
            autoLoad: this.autoLoad
        };
        if (this.useInsertAdd) gridConfig.onAdd = this.onAdd;
        this.grid = new Vps.Auto.GridPanel(gridConfig);
        this.proxyItem = this.grid;

        // Wenn ein Panel direkt, sonst Tabs
        this.editPanels = [];
        if (this.contentEditComponents.length == 1) {
            this.editPanels.push(Vps.Binding.AbstractPanel.createFormOrComponentPanel(
                this.componentConfigs, this.contentEditComponents[0],
                {region: 'center', title: null}, this.grid
            ));
            this.childPanel = this.editPanels[0];
        } else {
            this.contentEditComponents.each(function(ec) {
                this.editPanels.push(Vps.Binding.AbstractPanel.createFormOrComponentPanel(
                    this.componentConfigs, ec, {}, this.grid
                ));
            }, this);
            this.childPanel = new Ext.TabPanel({
                region: 'center',
                activeTab: 0,
                items: this.editPanels
            });
        }

        // MultiFileUpload hinzufügen falls konfiguriert
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
            items: westItems,
            collapsible : true,
            title: this.listTitle
        });

        this.items = [this.westPanel, this.childPanel];
        Vpc.Abstract.List.List.superclass.initComponent.call(this);
    },

    load: function()
    {
        this.grid.load();
        this.grid.selectId(false);
        
        // Alle Forms leeren wenn Seite neu geladen wird
        this.editPanels.each(function(panel) {
            panel.setBaseParams({});
            if (panel.getForm) {
                var f = panel.getForm();
                if (f) {
                    f.clearValues();
                    f.clearInvalid();
                }
            }
            panel.disable();
        }, this);
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
