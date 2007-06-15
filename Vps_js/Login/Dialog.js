Vps.Login.Dialog = function(renderTo, config)
{
    Ext.apply(this, config);

    this.dialog = new Ext.BasicDialog(renderTo, {
        height: 150,
        width: 310,
        minHeight: 100,
        minWidth: 150,
        model: true,
        proxyDrag: true,
        shadow: true,
        title: 'Login',
        closable: false
    });
    this.dialog.addKeyListener(27, this.dialog.hide, this); // ESC can also close the dialog


    this.form = new Ext.form.Form({
            labelWidth: 110, 
            url:'/user/jsonLoginUser'
        });

    this.form.add(
            new Ext.form.TextField({
                    fieldLabel: 'Benutzername',
                    name: 'username',
                    width: 150,
                    allowBlank: false
                }),
            new Ext.form.TextField({
                    inputType: 'password',
                    fieldLabel: 'Passwort',
                    name: 'password',
                    width: 150,
                    allowBlank: false
                })
    );

    this.dialog.addButton('OK', function() {
        this.form.submit({
            success: this.onSubmitSuccess,
            scope: this
            });
    }, this);

    this.form.render(this.dialog.body);
};


Ext.extend(Vps.Login.Dialog, Ext.util.Observable,
{
    showLogin: function() {
        this.dialog.show();
    },
    onSubmitSuccess: function() {
        this.dialog.hide();
        Ext.callback(this.success, this.scope);
    }
});
