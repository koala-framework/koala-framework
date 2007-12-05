Ext.namespace('Vps.User');

Vps.User.Users = Ext.extend(Vps.Auto.GridPanel, {
    initComponent: function()
    {
        if (!this.controllerUrl) this.controllerUrl = '/users/';

        Vps.User.Users.superclass.initComponent.call(this);
        
        this.on('rendergrid', function(grid) {
            var passwordAction = this.editDialog.getAction('password');
            passwordAction.setHandler(this.editDialog.onMailsend, this);
            grid.getTopToolbar().add('-');
            grid.getTopToolbar().add(passwordAction);
            this.on('rowselect', function(selData, gridRow, currentRow) {
                if (currentRow.data.email != '' && currentRow.data.id != 0) {
                    passwordAction.enable();
                } else {
                    passwordAction.disable();
                }
            }, this);
        }, this);
    }
});