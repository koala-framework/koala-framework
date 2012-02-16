Ext.namespace('Kwc.Directories.Item.Directory');
Kwc.Directories.Item.Directory.EditFormPanel = Ext.extend(Kwf.Auto.FormPanel,
{
    lastComponentId: null,
    setBaseParams : function(baseParams) {
        Kwc.Directories.Item.Directory.EditFormPanel.superclass.setBaseParams.apply(this, arguments);
        this._baseParamsChanged();

    },
    applyBaseParams : function(baseParams) {
        Kwc.Directories.Item.Directory.EditFormPanel.superclass.applyBaseParams.apply(this, arguments);
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

Ext.reg('kwc.directories.item.directory.form', Kwc.Directories.Item.Directory.EditFormPanel);
