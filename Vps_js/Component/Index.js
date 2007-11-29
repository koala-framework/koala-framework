Ext.namespace('Vps.Component');
Vps.Component.Index = Ext.extend(Ext.Panel, {
    initComponent : function()
    {
	    this.html = 'Willkommen bei VPS.';
        Vps.Component.Index.superclass.initComponent.call(this);
    }
});
