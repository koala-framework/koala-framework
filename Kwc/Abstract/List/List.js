Ext.namespace('Kwc.Abstract.List');
Kwc.Abstract.List.List = Ext.extend(Kwf.Binding.ProxyPanel,
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
        if (this.useInsertAdd) {
            gridConfig.onAdd = this.onAdd.createDelegate(this); // wg. Scope
        }

        this.grid = new Kwf.Auto.GridPanel(gridConfig);
        this.proxyItem = this.grid;

        if (!this.editForms) this.editForms = [];

        // Wenn ein Panel direkt, sonst Tabs
        this.editPanels = [];
        if (this.editForms.length==0 && this.contentEditComponents.length == 1) {
            this.editPanels.push(Kwf.Binding.AbstractPanel.createFormOrComponentPanel(
                this.componentConfigs, this.contentEditComponents[0],
                {region: 'center', title: null}, this.grid
            ));
            this.childPanel = this.editPanels[0];
        } else {
            this.editForms.each(function(ef) {
                ef.baseParams = Kwf.clone(this.getBaseParams());
                var panel = Ext.ComponentMgr.create(ef);
                this.grid.addBinding(panel);
                this.editPanels.push(panel);
            }, this);
            this.contentEditComponents.each(function(ec) {
                this.editPanels.push(Kwf.Binding.AbstractPanel.createFormOrComponentPanel(
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
            this.multiFileUploadPanel = new Kwf.Utils.MultiFileUploadPanel(Ext.applyIf({
                border: false,
                region: 'south',
                height: 50,
                bodyStyle: 'padding-top: 15px; padding-left:80px;',
                controllerUrl: this.controllerUrl,
                baseParams: this.baseParams,
                maxEntriesErrorMessage: this.maxEntriesErrorMessage
            }), this.multiFileUpload);
            this.multiFileUploadPanel.on('uploaded', function() {
                this.grid.reload();
            }, this);
            westItems.push(this.multiFileUploadPanel);
        }

        if (this.maxEntries && this.multiFileUploadPanel) {
            this.grid.on('load', function() {
                if (this.maxEntries - this.grid.getStore().getCount()) {
                    this.multiFileUploadPanel.maxNumberOfFiles = this.maxEntries - this.grid.getStore().getCount();
                }
            }, this);
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
        Kwc.Abstract.List.List.superclass.initComponent.call(this);
    },

    load: function()
    {
        this.grid.load();
        this.grid.selectId(false);
        
        // Alle Forms leeren wenn Seite neu geladen wird
        this.editPanels.each(function(panel) {
            panel.setBaseParams(this.getBaseParams());
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
                this.grid.getSelectionModel().clearSelections();
                this.reload({
                    callback: function(o, r, s) {
                        this.grid.getSelectionModel().selectLastRow();
                        if (this.childPanel.setActiveTab) {
                            this.childPanel.setActiveTab(0);
                        }
                    },
                    scope: this
                });
            },
            scope: this
        });
    },

    applyBaseParams: function(baseParams) {
        if (this.multiFileUploadPanel) {
            this.multiFileUploadPanel.applyBaseParams(baseParams);
        }
        return Kwc.Abstract.List.List.superclass.applyBaseParams.apply(this, arguments);
    },
    setBaseParams : function(baseParams) {
        if (this.multiFileUploadPanel) {
            this.multiFileUploadPanel.setBaseParams(baseParams);
        }
        return Kwc.Abstract.List.List.superclass.setBaseParams.apply(this, arguments);
    }
});
Ext.reg('kwc.list.list', Kwc.Abstract.List.List);
