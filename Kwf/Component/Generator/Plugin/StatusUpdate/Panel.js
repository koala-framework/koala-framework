Ext.ns('Kwf.Component.Generator.Plugin.StatusUpdate.Panel');
Kwf.Component.Generator.Plugin.StatusUpdate.Panel = Ext.extend(Kwf.Binding.ProxyPanel, {
    layout: 'border',
    border: false,
    initComponent: function() {
        this.proxyItem = new Kwf.Auto.GridPanel({
            controllerUrl: this.logControllerUrl,
            region: 'center'
        });
        this.actions.send = new Ext.Action({
            text    : trlKwf('Send'),
            icon    : '/assets/silkicons/comments.png',
            cls     : 'x-btn-text-icon',
            handler : this.send,
            scope   : this
        });
        this.tbar = [ this.actions.send ];

        this.textArea = new Ext.form.TextArea({
            fieldLabel: trlKwf('Text'),
            width: 400,
            height: 100,
            maxLength: 140
        });

        this.checkboxes = [];
        this.backends.each(function(i) {
            this.checkboxes.push(new Ext.form.Checkbox({
                boxLabel: i.label,
                name: i.name,
                checked: true,
                labelSeparator: ''
            }));
        }, this);

        var items = [this.textArea];
        this.checkboxes.each(function(i) {
            items.push(i);
        }, this);
        this.form = new Ext.FormPanel({
            bodyStyle: 'padding:10px',
            region: 'north',
            height: 200,
            items: items
        });
        this.items = [this.proxyItem, this.form];
        Kwf.Component.Generator.Plugin.StatusUpdate.Panel.superclass.initComponent.call(this);
    },

    load : function(params, options) {
        Kwf.Component.Generator.Plugin.StatusUpdate.Panel.superclass.load.apply(this, arguments);
        Ext.Ajax.request({
            url: this.controllerUrl+'/json-default-text',
            params: this.getBaseParams(),
            mask: this.el,
            success: function(response, options, result) {
                this.textArea.setValue(result.text);
            },
            scope: this
        });
    },

    send: function() {
        if (!this.form.getForm().isValid()) {
            Ext.Msg.alert(trlKwf('Save'),
                trlKwf("Can't save, please fill all red underlined fields correctly."));
            return;
        }

        var services = [];
        this.checkboxes.each(function(i) {
            if (i.getValue()) {
                services.push(i.getName());
            }
        }, this);

        var params = Kwf.clone(this.getBaseParams());
        params.text = this.textArea.getValue();
        params.services = services.join(',');

        delete window.authCallback;
        Ext.Msg.hide();
        Ext.Ajax.request({
            url: this.controllerUrl+'/json-send',
            params: params,
            mask: this.el,
            success: function(response, options, result) {
                if (result.requestAuthUrl) {
                    Ext.Msg.show({
                        title: trlKwf('Authenticate'),
                        msg: trlKwf('You are not yet authenticated to {0}. Do you want to authenticate now?', result.backendName),
                        buttons: Ext.Msg.OKCANCEL,
                        fn: function(button) {
                            if (button == 'ok') {
                                window.authCallback = this.send;
                                window.authCallbackScope = this;
                                this.authWindow = window.open(result.requestAuthUrl);
                                Ext.Msg.show({
                                    title: trlKwf('Authenticate'),
                                    msg: trlKwf('Please confirm the authentification in the opened popup'),
                                    buttons: Ext.Msg.CANCEL,
                                    fn: function(button) {
                                        if (button == 'cancel') {
                                            delete window.authCallback;
                                            this.authWindow.close();
                                        }
                                    },
                                    scope: this
                                });
                            }
                        },
                        scope: this,
                        icon: Ext.MessageBox.INFO
                    });
                } else {
                    this.proxyItem.reload();
                    Ext.Msg.show({
                        title: trlKwf('Success'),
                        msg: trlKwf('Status Update successfully posted.'),
                        buttons: Ext.Msg.OK
                    });
                }
            },
            scope: this
        });
    }
});
Ext.reg('kwf.component.generator.plugin.statusUpdate', Kwf.Component.Generator.Plugin.StatusUpdate.Panel);
