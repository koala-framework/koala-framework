Kwc.TableGridPanel = Ext.extend(Kwf.Auto.GridPanel,
{
    initComponent : function()
    {
        this.gridConfig = { selModel: new Ext.grid.CheckboxSelectionModel() };
        Kwc.TableGridPanel.superclass.initComponent.call(this);
    }
});
Ext.reg('kwc.tablegridpanel', Kwc.TableGridPanel);
