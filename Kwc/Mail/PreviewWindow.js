Ext2.ns('Kwc.Mail');
Kwc.Mail.PreviewWindow = Ext2.extend(Ext2.Window, {
    initComponent : function()
    {
        this.button = [];
        this.button['html'] = new Ext2.Toolbar.Button ({
            icon    : '/assets/silkicons/html.png',
            cls     : 'x2-btn-text-icon',
            text	: trlKwf('HTML'),
            enableToggle: true,
            toggleGroup: 'format',
            pressed : false,
            toggleHandler: this.toggleButton,
            scope: this,
            name : 'html'
        });
        this.button['text'] = new Ext2.Toolbar.Button ({
            icon    : '/assets/silkicons/page_white_text.png',
            cls     : 'x2-btn-text-icon',
            text	: trlKwf('Text'),
            enableToggle: true,
            toggleGroup: 'format',
            pressed : false,
            toggleHandler: this.toggleButton,
            scope: this,
            name: 'text'
        });

        this.title = trlKwf('Newsletter Preview');
        this.buttons = [new Ext2.Action({
            text    : trlKwf('Close'),
            handler : function() {
                this.hide();
            },
            scope   : this
        })];
        this.tbar = [];

        var send = new Ext2.Toolbar.Button ({
            icon    : '/assets/silkicons/email_go.png',
            cls     : 'x2-btn-text-icon',
            text	: trlKwf('Send'),
            handler : function(a, b, c) {
                Ext2.Ajax.request({
                    url : this.controllerUrl + '/json-send-mail',
                    params: Ext2.apply(this.baseParams, {
                        address: this.address.getValue(),
                        format: this.button['html'].pressed ? 'html' : 'text'
                    }) ,
                    success: function(response, options, r) {
                        Ext2.MessageBox.alert(trlKwf('Status'), r.message);
                    },
                    scope: this
                });
            },
            scope   : this
        });

        this.address = new Ext2.form.TextField({
            width: 200,
            vtype: 'email'
        });
        this.subject = new Ext2.StatusBar({});
        this.mailPanel = new Ext2.Panel({
            autoScroll: true,
            tbar	: this.subject,
            bodyCssClass: 'mailPreviewPanel'
        });

        this.items = [this.mailPanel];
        this.autoScroll = true;
        this.closeAction = 'hide';

        this.tbar.add(this.button['html'], this.button['text'], '|', this.address, send);
        this.baseParams = {};
        Kwc.Mail.PreviewWindow.superclass.initComponent.call(this);
    },

    showEdit : function(id, record)
    {
        this.show(trlKwf('Loading...'));
        this.mailPanel.body.dom.style.backgroundColor = '#FFFFFF';
        this.mailPanel.body.dom.innerHTML = '';
        this.subject.showBusy();
        Ext2.Ajax.request({
            url: this.controllerUrl + '/json-data',
            params		:  Ext2.apply(this.baseParams, {
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
    	Ext2.apply(this.baseParams, baseParams);
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
Ext2.reg('kwc.mail.preview', Kwc.Mail.PreviewWindow);
