Ext.ns('Vpc.Newsletter.Detail');
Vpc.Newsletter.Detail.MailPanel = Ext.extend(Ext.Window, {
    initComponent : function()
    {
		this.title = trlVps('Newsletter Preview');
		this.buttons = [new Ext.Action({
            text    : trlVps('Close'),
            handler : function() {
                this.hide();
            },
            scope   : this
        })];
		this.tbar = [];
		Vpc.Newsletter.Detail.MailPanel.superclass.initComponent.call(this);
    },
	showEdit : function(id, record)
	{
	    this.show();
        Ext.Ajax.request({
            url: this.controllerUrl + '/json-data',
	        params		:  Ext.apply(this.baseParams, {
	        	id : id
            }),
            success: function(r, options, data) {
        		this.body.dom.style.backgroundColor = '#FFFFFF';
        		this.body.dom.innerHTML = data.html;
            },
            scope: this
        });
	}
});
Ext.reg('vpc.newsletter.mailpanel', Vpc.Newsletter.Detail.MailPanel);
