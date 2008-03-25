Vps.Connection = Ext.extend(Ext.data.Connection, {
    request: function(options)
    {

        if (options.url.match(/[\/a-zA-Z0-9]*\/json[a-zA-Z0-9]+(\/|\?|)/)) {

            if (options.mask) {
                if (Vps.Connection.masks == 0) {
                    if (Ext.get('loading')) {
                        Ext.getBody().mask();
                    } else {
                        Ext.getBody().mask(trlVps('Loading...'));
                    }
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
        }
        if (!options.params) options.params = {};
        options.params.application_version = Vps.application.version;
        if (!options.url.match(':\/\/')) {
            //absolute url incl. http:// erstellen
            //wird benötigt wenn fkt über mozrepl aufgerufen wird
            var u = location.protocol + '/'+'/' + location.host;
            if (options.url.substr(0, 1) == '/') {
                options.url = u + options.url;
            } else {
                options.url = u + '/' + options.url;
            }
        }
        Vps.Connection.superclass.request.call(this, options);
    },
    repeatRequest: function(options) {
        delete options.vpsIsSuccess;
        Vps.Connection.superclass.request.call(this, options);
    },
    vpsSuccess: function(response, options)
    {
        options.vpsIsSuccess = false;
        options.vpsLogin = false;
        try {
            var r = Ext.decode(response.responseText);
        } catch(e) {
            var p;
            if (typeof options.params == "string") {
                p = options.params;
            } else {
                p = Ext.urlEncode(options.params);
            }
            var errorMsg = '<a href="'+options.url+'?'+p+'">request-url</a><br />';
            errorMsg += e.toString()+': <br />'+response.responseText;
            var errorMsgTitle = trlVps('Javascript Parse Exception');        }

        if (!errorMsg && r.exception) {
            var p;
            if (typeof options.params == "string") {
                p = options.params;
            } else {
                p = Ext.urlEncode(options.params);
            }
            var errorMsg = '<a href="'+options.url+'?'+p+'">request-url</a><br />';
            errorMsg += r.exception;
            var errorMsgTitle = trlVps('PHP Exception');
        }
        if (errorMsg) {
            if (Vps.debug) {
                Ext.Msg.show({
                    title: errorMsgTitle,
                    msg: errorMsg,
                    buttons: Ext.Msg.OK,
                    modal: true,
                    width: 800
                });
            } else {
                Ext.Msg.alert(trlVps('Error'), trlVps("A Server failure occured."));
                Ext.Ajax.request({
                    url: '/vps/error/error/jsonMail',
                    params: {msg: errorMsg}
                });
            }
            Ext.callback(options.vpsCallback.failure, options.vpsCallback.scope, [response, options]);
            return;
        }

        if (!r.success) {
            if (r.wrongversion) {
                Ext.Msg.alert(trlVps('Error - wrong version'),
                trlVps('Because of an application update the application has to be reloaded.'),
                function(){
                    location.reload();
                });
                Ext.callback(options.vpsCallback.failure, options.vpsCallback.scope, [response, options]);
                return;
            }
            if (r.login) {
                options.vpsLogin = true;
                var dlg = new Vps.User.Login.Dialog({
                    message: r.message,
                    success: function() {
                        //redo action...
                        this.repeatRequest(options);
                    },
                    scope: this
                });
                Ext.getBody().unmask();
                dlg.showLogin();
                return;
            }
            if (r.error) {
                Ext.Msg.alert(trlVps('Error'), r.error);
            } else if (!r.login) {
                Ext.Msg.alert(trlVps('Error'), trlVps("A Server failure occured."));
            }
            Ext.callback(options.vpsCallback.failure, options.vpsCallback.scope, [response, options]);
            return;
        }
        options.vpsIsSuccess = true;

        Vps.callWithErrorHandler(function() {
            Ext.callback(options.vpsCallback.success, options.vpsCallback.scope, [response, options, r]);
        });
    },

    vpsFailure: function(response, options)
    {
        options.vpsIsSuccess = false;
        var debugString = '';
        for (var dbg in options.params) {
            debugString += '<br />params.' + dbg + ' = ' + options.params[dbg];
        }
        Ext.Msg.alert(trlVps('Error'), trlVps("A connection problem occured.")+"<br /><br /><b>"+trlVps("Debug info")+":</b><br />"
            + "url: " + options.url + debugString);
        Ext.callback(options.vpsCallback.failure, options.vpsCallback.scope, [response, options]);
        return;
    },

    vpsCallback: function(options, success, response)
    {
        //wenn login-fenster angezeigt wird keinen callback aufrufen - weil der request
        //wird ja erneut gesendet und da dann der callback aufgerufen.
        if (options.vpsLogin) return;

        if (options.mask) {
            Vps.Connection.masks--;
            if (Vps.Connection.masks == 0) {
                Ext.getBody().unmask();
                if (Ext.get('loading')) {
                    Ext.get('loading').fadeOut({remove: true});
                }
            }
        }

        if(success && !options.vpsIsSuccess) {
            success = false;
        }
        Ext.callback(options.vpsCallback.callback, options.vpsCallback.scope, [options, success, response]);
    }
});
Vps.Connection.masks = 0; //static var that hols number of masked requests

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

