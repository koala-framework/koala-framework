Ext.namespace('Vps.User');

Vps.User.Useredit = Ext.extend(Vps.Auto.Form.Window, {
    initComponent: function()
    {
        this.actions = {};
        this.actions.password = new Ext.Action({
            icon    : '/assets/silkicons/email_go.png',
            cls     : 'x-btn-text-icon',
            text    : 'Save & Mail Access Data'
        });
        var passwordAction = this.getAction('password');
        passwordAction.setHandler(this.onMailsend, this);
        this.buttons = [
            passwordAction, 
            this.getAction('cancel'), 
            this.getAction('save')
        ];
        this.controllerUrl = '/admin/component/useredit';
        this.width = 500;
        this.height = 400;
        Vps.User.Useredit.superclass.initComponent.call(this);
    },
    
    onMailsend : function() {
        Ext.Msg.show({
            title:'Send Userdata?',
            msg: 'Do you really want to generate a new password for this user?<br><br>'
                +'<i>The password will be sent to the user\'s current E-Mail address.</i>',
            buttons: Ext.Msg.YESNO,
            scope: this,
            fn: function(button) {
                if (button == 'yes') {
                    if (this.getAutoForm) { // AutoForm
                        this.getAutoForm().submit({
                            success: function() {
                                Ext.get(document.body).mask('sending...', 'x-mask-loading');
                                Ext.Ajax.request({
                                    url: this.controllerUrl+'/jsonMailsend',
                                    params: {id: this.getForm().baseParams.id},
                                    success:function() {
                                        Ext.get(document.body).unmask();
                                        this.hide();
                                    },
                                    scope : this
                                });
                            },
                            scope: this
                        });
                    } else { // AutoGrid
                        var selectedRow = this.getSelected();
                        if (!selectedRow) return;
                        if (selectedRow.data.id == 0) return;
                        Ext.get(document.body).mask('senden...', 'x-mask-loading');
                        Ext.Ajax.request({
                            url: this.controllerUrl+'/jsonMailsend',
                            params:{id:selectedRow.id},
                            success:function() {
                                this.reload();
                            },
                            callback: function() {
                                Ext.get(document.body).unmask();
                            },
                            scope : this
                        });
                    }
                }
            }
        });
    }
});