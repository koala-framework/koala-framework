Ext.ns('Vpc.Mail');
Vpc.Mail.PreviewWindow = Ext.extend(Ext.Window, {
    initComponent : function()
    {
        this.button = [];
        this.button['html'] = new Ext.Toolbar.Button ({
            icon    : '/assets/silkicons/html.png',
            cls     : 'x-btn-text-icon',
            text	: trlVps('HTML'),
            enableToggle: true,
            toggleGroup: 'format',
            pressed : false,
            toggleHandler: this.toggleButton,
            scope: this,
            name : 'html'
        });
        this.button['text'] = new Ext.Toolbar.Button ({
            icon    : '/assets/silkicons/page_white_text.png',
            cls     : 'x-btn-text-icon',
            text	: trlVps('Text'),
            enableToggle: true,
            toggleGroup: 'format',
            pressed : false,
            toggleHandler: this.toggleButton,
            scope: this,
            name: 'text'
        });

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
                    params: Ext.apply(this.baseParams, {
                        address: this.address.getValue(),
                        format: this.button['html'].pressed ? 'html' : 'text'
                    }) ,
                    success: function(response, options, r) {
                        Ext.MessageBox.alert(trlVps('Status'), r.message);
                    },
                    scope: this
                });
            },
            scope   : this
        });

        this.address = new Ext.form.TextField({
            width: 200,
            vtype: 'email'
        });
        this.subject = new Ext.StatusBar({});
        this.mailPanel = new Ext.Panel({
            autoScroll: true,
            tbar	: this.subject,
            bodyCssClass: 'mailPreviewPanel'
        });

        this.items = [this.mailPanel];
        this.autoScroll = true;
        this.closeAction = 'hide';

        this.tbar.add(this.button['html'], this.button['text'], '|', this.address, send);
        this.baseParams = {};
        Vpc.Mail.PreviewWindow.superclass.initComponent.call(this);
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
                this.currentId = id;
                this.html = data.html;
                this.text = data.text;
                this.mailPanel.body.dom.innerHTML = this.html;
                this.button[data.format].toggle(true);
                this.subject.clearStatus();
                this.subject.setText(data.subject);
            },
            scope: this
        });
    },

    applyBaseParams : function(baseParams) {
    	Ext.apply(this.baseParams, baseParams);
    },

    toggleButton : function(button, pressed)
    {
        if (pressed) {
            if (button.name == 'html') {
                this.mailPanel.body.dom.innerHTML = this.html;
            } else {
                this.mailPanel.body.dom.innerHTML = this.text;
            }
        }
    }
});
Ext.reg('vpc.mail.preview', Vpc.Mail.PreviewWindow);
