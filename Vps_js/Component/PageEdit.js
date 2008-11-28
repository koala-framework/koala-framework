Ext.namespace('Vps.Component');
Vps.Component.PageEdit = Ext.extend(Vps.Auto.Form.Window, {
	
	filtered: false,
	
    initComponent : function()
    {
		this.on('beforeloadform', function(form, data) {
			this.findField('component').store.filter('domain', data.domain);
			// Das hier braucht man nur, weil beim ersten Mal nicht gefiltert wird...
			var c = this.findField('component');
			if (!this.foo) {
				c.on('expand', function() {
					if (!this.filtered) {
						c.store.filter('domain', data.domain);
						this.filtered = true;
					}
				}, this);
			}
		}, this);
		Vps.Component.PageEdit.superclass.initComponent.call(this);
    }
});
