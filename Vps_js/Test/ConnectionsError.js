Ext.namespace('Vps.Test');

Vps.Test.ConnectionError = Ext.extend(Ext.Panel, {
	html: 'test',
    initComponent: function()
    {
        Vps.Test.ConnectionError.superclass.initComponent.call(this);
	    Ext.onReady(function() {
	         Ext.Ajax.request({
	            timeout: 1000,
	            params: {test:1},
	            url: '/vps/error/error/json-timeout',
				failure: function() {
					this.el.overwrite('blubbbb');
				},
				scope: this
	        });
	    });
    }
});
