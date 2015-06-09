Kwc.TableGridPanel = Ext2.extend(Kwf.Auto.GridPanel,
{
    initComponent : function()
    {
        this.gridConfig = { selModel: new Ext2.grid.CheckboxSelectionModel() };
        Kwc.TableGridPanel.superclass.initComponent.call(this);
    }
});
Ext2.reg('kwc.tablegridpanel', Kwc.TableGridPanel);
