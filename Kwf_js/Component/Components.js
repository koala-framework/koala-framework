Ext2.namespace('Kwf.Component');
Kwf.Component.Components = Ext2.extend(Kwf.Auto.SyncTreePanel, {
    initComponent : function()
    {
        this.controllerUrl = '/admin/component/components';
        Kwf.Component.Components.superclass.initComponent.call(this);
    }
});
