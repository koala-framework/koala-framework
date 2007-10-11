Vps.Connection = function(config){
   Vps.Connection.superclass.constructor.call(this, config);
};
Vps.Connection.masks = 0; //static var that hols number of masked requests
Ext.extend(Vps.Connection, Ext.data.Connection, {
    request: function(options)
    {
        if (options.mask) {
            if (Vps.Connection.masks == 0) {
                Ext.getBody().mask('Loading...');
            }
            Vps.Connection.masks++;
        }
        options.vpsCallback = {
            success: options.success,
            failure: options.failure,
            callback: options.callback,
            scope: options.scope
        };
        options.success = this.vpsSuccess;
        options.failure = this.vpsFailure;
        options.callback = this.vpsCallback;
        options.scope = this;
        Vps.Connection.superclass.request.call(this, options);
    },
    repeatRequest: function(options) {
        delete options.vpsIsSuccess;
        Vps.Connection.superclass.request.call(this, options);
    },
    vpsSuccess: function(response, options)
    {
        Vps.Connection.masks--;
        if (Vps.Connection.masks == 0) {
            Ext.getBody().unmask();
        }
        options.vpsIsSuccess = false;
        var r = Ext.decode(response.responseText);
        if (r.exceptions) {
            Ext.Msg.alert('Exceptions', "Folgende Exceptions sind aufgetreten:\n"+r.exceptions);
            Ext.callback(options.vpsCallback.failure, options.vpsCallback.scope, [response, options]);
            return;
        }

        if (!r.success) {
            if (r.login && r.login===true) {
                dlg = new Vps.User.Login.Dialog(Ext.get(document.body).createChild(), {
                    success: function() {
                        //redo action...
                        this.repeatRequest(options);
                    },
                    scope: this
                });
                dlg.showLogin();
                Ext.callback(options.vpsCallback.failure, options.vpsCallback.scope, [response, options]);
                return;
            }
            if (r.error) {
                Ext.Msg.alert('Fehler', r.error);
            } else if (!r.login) {
                Ext.Msg.alert('Fehler', "Ein Serverfehler ist aufgetreten.");
            }
            Ext.callback(options.vpsCallback.failure, options.vpsCallback.scope, [response, options]);
            return;
        }
        options.vpsIsSuccess = true;

        Ext.callback(options.vpsCallback.success, options.vpsCallback.scope, [response, options, r]);
    },

    vpsFailure: function(response, options)
    {
        options.vpsIsSuccess = false;
        Ext.Msg.alert('Fehler', "Ein Verbindungsfehler ist aufgetreten.");
        Ext.callback(options.vpsCallback.failure, options.vpsCallback.scope, [response, options]);
        return;
    },
    
    vpsCallback: function(options, success, response)
    {
        if(success && !options.vpsIsSuccess) {
            success = false;
        }
        Ext.callback(options.vpsCallback.callback, options.vpsCallback.scope, [options, success, response]);
    }
});

Ext.Ajax = new Vps.Connection({
    /**
     * The timeout in milliseconds to be used for requests. (defaults
     * to 30000)
     * @type Number
     * @property  timeout
     */
    autoAbort : false,

    /**
     * Serialize the passed form into a url encoded string
     * @return {String}
     */
    serializeForm : function(form){
        return Ext.lib.Ajax.serializeForm(form);
    }
});

