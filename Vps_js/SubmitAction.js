Ext.form.Action.VpsSubmit = function(form, options){
    Ext.form.Action.VpsSubmit.superclass.constructor.call(this, form, options);
};

Ext.extend(Ext.form.Action.VpsSubmit, Ext.form.Action.Submit, {
    handleResponse : function(response){
        var r = Ext.decode(response.responseText);
        if (r.exceptions) {
            Ext.Msg.alert('Exceptions', "Folgende Exceptions sind aufgetreten:\n"+o.exceptions);
            return { success: false };
        }
        if (!r.success) {
            if (r.login && r.login===true) {
                dlg = new Avs.Login.Dialog(Ext.get(document.body).createChild(), {
                    success: function() {
                        //redo action...
                        this.run();
                    },
                    scope: this
                });
                dlg.showLogin();
                return { success: false };
            }
            if (r.error) {
                Ext.Msg.alert('Fehler', r.error);
            } else if (!r.login) {
                Ext.Msg.alert('Fehler', "Ein Fehler ist aufgetreten.");
            }
        }
        return r;
    }
});
Ext.form.Action.ACTION_TYPES.submit = Ext.form.Action.VpsSubmit; //normale submit-aktion überschreiben
