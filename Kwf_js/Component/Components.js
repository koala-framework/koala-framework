Ext.namespace('Vps.Component');
Vps.Component.Components = Ext.extend(Vps.Auto.SyncTreePanel, {
    initComponent : function()
    {
        this.controllerUrl = '/admin/component/components';
        Vps.Component.Components.superclass.initComponent.call(this);
    }
});
