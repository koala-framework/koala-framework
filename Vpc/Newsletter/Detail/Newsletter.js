Ext.ns('Vpc.Newsletter');
Vpc.Newsletter.Panel = Ext.extend(Ext.Panel, {
    initComponent: function() {
	Vpc.Newsletter.Panel.superclass.initComponent.call(this);
    }
});
Ext.reg('Vpc.Newsletter', Vpc.Newsletter.Panel);
