Vps.Connection = function(config){
    Vps.Connection.superclass.constructor.call(this, config);
};
Ext.extend(Vps.Connection, Ext.data.Connection, {
    request: function(options) {
        options.originalCallback = options.callback;
        options.originalScope = options.scope;
        options.callback = this.vpsCallback;
        options.scope = this;
        Vps.Connection.superclass.request.call(this, options);
    },
    redoRequest: function(options) {
        Vps.Connection.superclass.request.call(this, options);
    },
    vpsCallback: function(options, success, response){
        if(!success) {
            Ext.Msg.alert('Fehler', "Ein Fehler ist aufgetreten.");
            if(options.failure) options.failure.call(options.originalScope, null);
            return;
        }
        var r = Ext.decode(response.responseText);
        if (r.exceptions) {
            Ext.Msg.alert('Exceptions', "Folgende Exceptions sind aufgetreten:\n"+r.exceptions);
            if(options.failure) options.failure.call(options.originalScope, r);
            return;
        }
        if (!r.success) {
            if (r.login && r.login===true) {
                dlg = new Avs.Login.Dialog(Ext.get(document.body).createChild(), {
                    success: function() {
                        //redo action...
                        this.redoRequest(options);
                    },
                    scope: this
                });
                dlg.showLogin();
                return;
            }
            if (r.error) {
                Ext.Msg.alert('Fehler', r.error);
            } else if (!r.login) {
                Ext.Msg.alert('Fehler', "Ein Fehler ist aufgetreten.");
            }
            return;
        }
        if(options.success) options.success.call(options.originalScope, r);
        if(options.originalCallback) options.originalCallback.call(options.originalScope, options, success, response);
    }
});
