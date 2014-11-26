Kwf.User.Grid.SendMailWindow = Ext2.extend(Ext2.Window,
{
    modal: true,
    initComponent: function() {
        this.formPanel = new Ext2.FormPanel({
            labelWidth: 90,
            url: this.controllerUrl+'/json-resend-mail',
            baseParams: this.baseParams,
            layout: 'fit',
            bodyStyle: 'background-color: transparent;',
            border: false,
            buttonAlign: 'right',
            items: {
                xtype: 'fieldset',
                title: trlKwf('Please choose'),
                defaultType: 'radio',
                autoHeight: true,
                items: [{
                    xtype: 'radio',
                    checked: true,
                    fieldLabel: trlKwf('E-Mail type'),
                    boxLabel: trlKwf('Activation'),
                    name: 'mailtype',
                    inputValue: 'activation'
                },{
                    xtype: 'radio',
                    fieldLabel: '',
                    labelSeparator: '',
                    boxLabel: trlKwf('Lost password'),
                    name: 'mailtype',
                    inputValue: 'lost_password'
                }]
            },
            buttons: [
                {
                    text: trlKwf('Send'),
                    handler: function() {
                        this.formPanel.buttons[0].disable();
                        this.formPanel.getForm().submit({
                            success: function() {
                                this.hide();
                            },
                            scope: this
                        });
                    },
                    scope: this
                }
            ]
        });

        this.linkTextArea = new Ext2.form.TextArea({
            width: 300,
            height: 30,
            readOnly: true
        });

        this.generateActivationLinkPanel = new Ext2.form.FieldSet({
            title: trlKwf('Generate a new activation link'),
            buttonAlign: 'left',
            layout: 'fit',
            autoHeight: true,
            items: [
                this.linkTextArea
            ],
            buttons: [
                {
                    text: trlKwf('Generate'),
                    handler: function() {
                        Ext2.Ajax.request({
                            url: this.controllerUrl+'/json-generate-activation-link',
                            params: this.baseParams,
                            mask: this.el,
                            success: function(response, options, r) {
                                this.linkTextArea.setValue(location.protocol+'//'+location.host+r.url);
                            },
                            scope: this
                        });
                    },
                    scope: this
                }
            ]

        });
        var infoPanel = new Ext2.Panel({
            bodyCssClass: 'userMailResendInfo',
            border: false,
            html: trlKwf('Please select the E-Mail type you wish to send to the user.')
        });
        var infoPanel2 = new Ext2.Panel({
            bodyCssClass: 'userMailResendInfo',
            border: false,
            html: trlKwf('Warning: this invalidates all previously generated activation links.')
        });
        this.title = trlKwf('Send a mail to a user');
        this.items = [ infoPanel, this.formPanel, this.generateActivationLinkPanel, infoPanel2 ];
        this.width = 450;
        this.height = 400;
        this.bodyStyle = 'padding: 15px;';
        this.buttons = [
            {
                text: trlKwf('Cancel'),
                handler: function() {
                    this.hide();
                },
                scope: this
            }
        ];
        Kwf.User.Grid.SendMailWindow.superclass.initComponent.call(this);
    }
});
