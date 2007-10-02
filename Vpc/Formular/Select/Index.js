Ext.namespace('Vpc.Formular.Select');
Vpc.Formular.Select.Index = Ext.extend(Ext.TabPanel,
{
    initComponent : function()
    {
        this.items = [
            new Vps.Auto.FormPanel({
                controllerUrl: this.controllerUrl,
                fitToFrame: true,
                title: 'Properties',
                bodyStyle : 'padding: 10px;'
            }),
            new Vps.Auto.GridPanel({
                controllerUrl: this.optionsControllerUrl,
                title: 'Options'
            })
        ];
        this.activeTab = 0;
        Vpc.Formular.Select.Index.superclass.initComponent.call(this);
    }
});