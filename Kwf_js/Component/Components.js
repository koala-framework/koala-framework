Ext.namespace('Kwf.Component');
Kwf.Component.Components = Ext.extend(Kwf.Auto.SyncTreePanel, {
    initComponent : function()
    {
        this.controllerUrl = '/admin/component/components';
        Kwf.Component.Components.superclass.initComponent.call(this);
    }
});
