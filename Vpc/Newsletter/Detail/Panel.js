Ext.ns('Vpc.Newsletter.Detail');
Vpc.Newsletter.Detail.Panel = Ext.extend(Ext.TabPanel, {

	initComponent: function() {

		var settings = new Vps.Auto.FormPanel({
	        controllerUrl   : this.controllerUrl,
	        baseParams		: {componentId: this.baseParams.componentId},
	        title           : trlVps('Settings')
	    });

        var mail = new Vps.Component.ComponentPanel({
            componentEditUrl: '/admin/component/edit',
            mainComponentClass: this.mailComponentClass,
            baseParams		: {componentId: this.baseParams.componentId + '-mail-content'},
            componentConfigs: this.componentConfigs,
            mainEditComponents: this.mainEditComponents,
            mainType	: this.mainType,
            title       	: trlVps('Mail')
        });
        
        var recipients = new Ext.Panel({
	        title           : trlVps('Recipients'),
	        layout: 'fit',
	        disabled: true
        });
        Ext.Ajax.request({
            url: this.recipientsControllerUrl + '/json-index',
            success: function(r, options, config) {
            	Ext.applyIf(config, {
	    	        baseControllerUrl: this.controllerUrl,
	    	        baseParams		: {componentId: this.baseParams.componentId}
	            });
	            var panel = Ext.ComponentMgr.create(config);
	            recipients.add(panel);
	            recipients.enable();
            },
            scope: this
        });

        var mailing = new Vpc.Newsletter.Detail.MailingPanel({
		    controllerUrl   : this.mailingControllerUrl,
		    pressedButton   : this.pressedMailingButton,
		    baseParams		: {componentId: this.baseParams.componentId},
		    title			: trlVps('Mailing'),
		    tbar			: []
		});

		this.deferredRender = true;
	    this.activeTab = 0;
	    this.items = [settings, mail, recipients, mailing];

    	Vpc.Newsletter.Detail.Panel.superclass.initComponent.call(this);
    }
});
Ext.reg('vpc.newsletter.panel', Vpc.Newsletter.Detail.Panel);
