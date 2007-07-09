Ext.namespace('Vps.User');
Vps.User.Users = function(renderTo, config)
{
    Ext.apply(this, config);

    this.grid = new Vps.Auto.Grid(null, {controllerUrl: '/users/'});

    this.grid.on('generatetoolbar', function(tb) {
        tb.addSeparator();
        this.passwordButton = tb.addButton({
            icon    : '/assets/vps/images/silkicons/email_go.png',
            cls     : 'x-btn-text-icon',
            text    : 'Neue Zugangsdaten mailen',
            disabled: true,
            handler : this.onMailsend,
            scope: this
        });
    }, this);
    this.grid.on('rowselect', function(selData, gridRow, currentRow) {
        if (currentRow.data.email != '') {
            this.passwordButton.enable();
        } else {
            this.passwordButton.disable();
        }
    }, this);

    var layout = new Vps.StandardLayout(renderTo);
    layout.add('center', new Ext.GridPanel(this.grid.grid));
    layout.endUpdate();
};

Ext.extend(Vps.User.Users, Ext.util.Observable,
{
    onMailsend : function() {
        Ext.Msg.show({
            title:'Benutzerdaten senden?',
            msg: 'Möchten Sie für diesen Benutzer wirklich eine neues Passwort generieren?<br><br>'
                +'<i>Info: Das Passwort wird an die eingetragene Email-Adresse gesendet.</i>',
            buttons: Ext.Msg.YESNO,
            scope: this,
            fn: function(button) {
                if (button == 'yes') {
                    var selectedRow = this.grid.getSelected();
                    if (!selectedRow) return;
                    if (selectedRow.data.id == 0) {
                        Ext.Msg.alert('Senden nicht möglich', 'Bitte klicken Sie erst auf speichern, bevor Sie neue Logindaten senden.<br><br>'
                                        +'<i>Info: Wird ein Account neu angelegt, werden die Logindaten automatisch gesendet, sofern eine gültige Email-Adresse angegeben wurde.');
                    } else {
                        Ext.get(document.body).mask('senden...', 'x-mask-loading');
                        Ext.Ajax.request({
                            url:'/users/jsonMailsend',
                            params:{id:selectedRow.id},
                            success:function() {
                                this.grid.reload();
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
            }
        });
    },
});