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
		
		var send = new Ext.Toolbar.Button ({
            icon    : '/assets/silkicons/email_go.png',
            cls     : 'x-btn-text-icon',
            text	: trlVps('Send'),
            handler : function(a, b, c) {
	    		Ext.Ajax.request({
                    url : this.controllerUrl + '/json-send-mail',
                    params: this.baseParams,
                    success: function(response, options, r) {
                        Ext.MessageBox.alert(trlVps('Status'), r.message);
                    },
                    scope: this
                });
            },
	        scope   : this
        });
		
		var address = new Ext.form.TextField({
	        width: 200,
	        vtype: 'email'
	    });
		this.subject = new Ext.StatusBar({});
		this.mailPanel = new Ext.Panel({
			autoScroll: true,
			tbar	: this.subject
		});

		this.items = [this.mailPanel];
		this.autoScroll = true;
		
		this.tbar.add(address, send);
		Vpc.Newsletter.Detail.MailPanel.superclass.initComponent.call(this);
    },
	showEdit : function(id, record)
	{
	    this.show(trlVps('Loading...'));
	    this.mailPanel.body.dom.style.backgroundColor = '#FFFFFF';
	    this.mailPanel.body.dom.innerHTML = '';
	    this.subject.showBusy();
        Ext.Ajax.request({
            url: this.controllerUrl + '/json-data',
	        params		:  Ext.apply(this.baseParams, {
	        	id : id
            }),
            success: function(r, options, data) {
        	    this.mailPanel.body.dom.innerHTML = data.html;
        	    this.subject.clearStatus();
        	    this.subject.setText(data.subject);
            },
            scope: this
        });
	},
	applyBaseParams : function(baseParams) {
        this.baseParams = baseParams;
    }
});
Ext.reg('vpc.newsletter.mailpanel', Vpc.Newsletter.Detail.MailPanel);
