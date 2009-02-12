Ext.namespace('Vps.User.Activate');

Vps.User.Activate.Index = Ext.extend(Ext.Panel,
{
    initComponent: function() {
        Vps.User.Activate.Index.superclass.initComponent.call(this);
        if (this.errorMsg != '') {
            Ext.Msg.show({
                title: 'Error',
                msg: this.errorMsg,
                buttons: Ext.Msg.OK
            });
        } else {
            dlg = new Ext.Window({
                layout: 'form',
                modal: true,
                labelWidth: 130,
                bodyStyle:'padding: 8px;',
                title: trlVps('Activate / Login'),
                width: 400,

                items: [{
                    html: trlVps('Please type in your password. After clicking the button below')+' '
                         +trlVps('you are logged in automatically and may use the typed in password'+' ')
                         +trlVps('for future logins.')+'<br />'+trlVps('Your email address:')+' <b>' + this.email + '</b>',
                    xtype: 'panel',
                    bodyStyle: 'background-color:transparent; padding:5px; margin-bottom:10px;'
                }, {
                    fieldLabel: trlVps('Password'),
                    name: 'password1',
                    allowBlank: false,
                    inputType: 'password',
                    xtype: 'textfield'
                }, {
                    fieldLabel: trlVps('Repeat password'),
                    name: 'password2',
                    allowBlank: false,
                    inputType: 'password',
                    xtype: 'textfield'
                }],

                buttons: [{ text: trlVps('Activate and login account'), handler: this.activateAction, scope: this }]
            });
            dlg.show();
        }
    },

    activateAction: function() {
        var password = document.getElementsByName('password1')[0].value;

        if (password != document.getElementsByName('password2')[0].value) {
            Ext.Msg.show({
                title: trlVps('Passwords not equal'),
                msg: trlVps('The repeated password is different - please try again.'),
                buttons: Ext.Msg.OK
            });
        } else {
            Ext.Ajax.request({
                url: '/vps/user/login/json-activate',
                params: { userId: this.userId, code: this.code, password: password },
                success: function(response, options, result) {
                    location.href = '/vps/welcome';
                }
            });
        }
    }
});
