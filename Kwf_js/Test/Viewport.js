Ext.namespace('Vps.Test');

Vps.Test.Viewport = Ext.extend(Ext.Viewport, {
	layout: 'fit',
    initComponent: function()
    {
        Vps.Test.Viewport.superclass.initComponent.call(this);
    }
});
