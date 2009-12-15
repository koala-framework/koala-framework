
Vps.User.Grid.SendMailWindow = Ext.extend(Ext.Window,
{
    initComponent: function() {
        this.formPanel = new Ext.FormPanel({
            labelWidth: 90,
            url: this.controllerUrl+'/json-resend-mail',
            baseParams: this.baseParams,
            layout: 'fit',
            bodyStyle: 'background-color: transparent;',
            border: false,
            buttonAlign: 'right',
            items: {
                xtype: 'fieldset',
                title: trlVps('Please choose'),
                defaultType: 'radio',
                autoHeight: true,
                items: [{
                    xtype: 'radio',
                    checked: true,
                    fieldLabel: trlVps('E-Mail type'),
                    boxLabel: trlVps('Activation'),
                    name: 'mailtype',
                    inputValue: 'activation'
                },{
                    xtype: 'radio',
                    fieldLabel: '',
                    labelSeparator: '',
                    boxLabel: trlVps('Lost password'),
                    name: 'mailtype',
                    inputValue: 'lost_password'
                }]
            },
            buttons: [
                {
                    text: trlVps('Send'),
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
                }, {
                    text: trlVps('Cancel'),
                    handler: function() {
                        this.hide();
                    },
                    scope: this
                }
            ]
        });
        var infoPanel = new Ext.Panel({
            bodyCssClass: 'userMailResendInfo',
            border: false,
            html: trlVps('Please select the E-Mail type you wish to send to the user.')
        });
        this.title = trlVps('Send a mail to a user');
        this.items = [ infoPanel, this.formPanel ];
        this.width = 450;
        this.height = 300;
        this.bodyStyle = 'padding: 15px;';
        Vps.User.Grid.SendMailWindow.superclass.initComponent.call(this);
    }
});
