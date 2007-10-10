Ext.namespace('Vps.User');

Vps.User.Users = Ext.extend(Vps.Auto.GridPanel, {
    initComponent: function()
    {
        if (!this.controllerUrl) this.controllerUrl = '/users/';

        var passwordAction = new Ext.Action({
                icon    : '/assets/vps/images/silkicons/email_go.png',
                cls     : 'x-btn-text-icon',
                text    : 'Neue Zugangsdaten mailen',
                disabled: true,
                handler : this.onMailsend,
                scope: this
            });

        Vps.User.Users.superclass.initComponent.call(this);

        this.on('rendergrid', function(grid) {
            grid.getTopToolbar().add('-');
            grid.getTopToolbar().add(passwordAction);
        }, this);
        this.on('rowselect', function(selData, gridRow, currentRow) {
            if (currentRow.data.email != '' && currentRow.data.id != 0) {
                passwordAction.enable();
            } else {
                passwordAction.disable();
            }
        }, this);

    },

    onMailsend : function() {
        Ext.Msg.show({
            title:'Benutzerdaten senden?',
            msg: 'Möchten Sie für diesen Benutzer wirklich eine neues Passwort generieren?<br><br>'
                +'<i>Info: Das Passwort wird an die eingetragene Email-Adresse gesendet.</i>',
            buttons: Ext.Msg.YESNO,
            scope: this,
            fn: function(button) {
                if (button == 'yes') {
                    var selectedRow = this.getSelected();
                    if (!selectedRow) return;
                    if (selectedRow.data.id == 0) return;
                    Ext.get(document.body).mask('senden...', 'x-mask-loading');
                    Ext.Ajax.request({
                        url: this.controllerUrl+'jsonMailsend',
                        params:{id:selectedRow.id},
                        success:function() {
                            this.reload();
                        },
                        failure: function() {
                            this.passwordButton.enable();
                        },
                        callback: function() {
                            Ext.get(document.body).unmask();
                        },
                        scope : this
                    });
                }
            }
        });
    }
});