Vpc.TableGridPanel = Ext.extend(Vps.Auto.GridPanel,
{
    initComponent : function()
    {
        this.gridConfig = { selModel: new Ext.grid.CheckboxSelectionModel() };
        Vpc.TableGridPanel.superclass.initComponent.call(this);
    }
});
Ext.reg('vpc.tablegridpanel', Vpc.TableGridPanel);
