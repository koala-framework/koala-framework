Ext.namespace('Vps.Component');
Vps.Component.PageEdit = Ext.extend(Vps.Auto.Form.Window, {
	
	filtered: false,
	filteredAdd: false,
	
    initComponent : function()
    {
		this.on('beforeloadform', function(form, data) {
			this.findField('component').store.filter('domain', data.domain);
			// Das hier braucht man nur, weil beim ersten Mal nicht gefiltert wird...
			var c = this.findField('component');
			if (!this.filtered) {
				c.on('expand', function() {
					if (!this.filtered) {
						c.store.filter('domain', data.domain);
						this.filtered = true;
					}
				}, this);
			}
		}, this);
		this.on('addaction', function(form, data) {
			this.findField('component').store.filter('domain', this.getAutoForm().baseParams.domain);
			// Das hier braucht man nur, weil beim ersten Mal nicht gefiltert wird...
			var c = this.findField('component');
			if (!this.filteredAdd) {
				c.on('expand', function() {
					if (!this.filteredAdd) {
						c.store.filter('domain', this.getAutoForm().baseParams.domain);
						this.filteredAdd = true;
					}
				}, this);
			}
		}, this);
		Vps.Component.PageEdit.superclass.initComponent.call(this);
    }
});
