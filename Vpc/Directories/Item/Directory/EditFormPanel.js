Ext.namespace('Vpc.Directories.Item.Directory');
Vpc.Directories.Item.Directory.EditFormPanel = Ext.extend(Vps.Auto.FormPanel,
{
    lastComponentId: null,
    setBaseParams : function(baseParams) {
        Vpc.Directories.Item.Directory.EditFormPanel.superclass.setBaseParams.apply(this, arguments);
        this._baseParamsChanged();

    },
    applyBaseParams : function(baseParams) {
        Vpc.Directories.Item.Directory.EditFormPanel.superclass.applyBaseParams.apply(this, arguments);
        this._baseParamsChanged();
    },

    //wenn die der componentId baseParameter geändert wurde können sich die meta-daten der Form ändern.
    //(zB Kategorien MultiFields)
    //daher formPanel löschen
    _baseParamsChanged: function() {
        if (this.formPanel && this.lastComponentId && this.getBaseParams().componentId != this.lastComponentId) {
            this.remove(this.formPanel, true);
            delete this.formPanel;
        }
        this.lastComponentId = this.getBaseParams().componentId;
    }
});
