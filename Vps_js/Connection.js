Vps.Connection = Ext.extend(Ext.data.Connection,
    request: function(option
   
        if (options.mask)
            if (Vps.Connection.masks == 0)
                if (Ext.get('loading'))
                    Ext.getBody().mask(
                } else
                    Ext.getBody().mask('Loading...'
               
           
            Vps.Connection.masks+
       
        options.vpsCallback =
            success: options.succes
            failure: options.failur
            callback: options.callbac
            scope: options.sco
        
        options.success = this.vpsSucces
        options.failure = this.vpsFailur
        options.callback = this.vpsCallbac
        options.scope = thi
        if (!options.params) options.params = {
        options.params.application_version = Vps.application.versio
        Vps.Connection.superclass.request.call(this, options
    
    repeatRequest: function(options)
        delete options.vpsIsSucces
        Vps.Connection.superclass.request.call(this, options
    
    vpsSuccess: function(response, option
   
        options.vpsIsSuccess = fals
        options.vpsLogin = fals
        try
            var r = Ext.decode(response.responseText
        } catch(e)
            var errorMsg = '<a href="'+options.url+'?'+Ext.urlEncode(options.params)+'">request-url</a><br />
            errorMsg += e.toString()+': <br />'+response.responseTex
            var errorMsgTitle = 'Javascript Parse Exception
       

        if (!errorMsg && r.exception)
            var errorMsg = '<a href="'+options.url+'?'+Ext.urlEncode(options.params)+'">request-url</a><br />
            errorMsg += r.exceptio
            var errorMsgTitle = 'PHP Exception
       
        if (errorMsg)
            if (Vps.debug)
                Ext.Msg.show
                    title: errorMsgTitl
                    msg: errorMs
                    buttons: Ext.Msg.O
                    modal: tru
                    width: 8
                }
            } else
                Ext.Msg.alert('Error', "A Server failure occured."
                Ext.Ajax.request
                    url: '/error/jsonMail
                    params: {msg: errorMs
                }
           
            Ext.callback(options.vpsCallback.failure, options.vpsCallback.scope, [response, options]
            retur
       

        if (!r.success)
            if (r.wrongversion)
                Ext.Msg.alert('Error - wrong version
                'Because of an application update the application has to be reloaded.
                function(
                    location.reload(
                }
                Ext.callback(options.vpsCallback.failure, options.vpsCallback.scope, [response, options]
                retur
           
            if (r.login)
                options.vpsLogin = tru
                var dlg = new Vps.User.Login.Dialog
                    success: function()
                        //redo action.
                        this.repeatRequest(options
                    
                    scope: th
                }
                Ext.getBody().unmask(
                dlg.showLogin(
                retur
           
            if (r.error)
                Ext.Msg.alert('Error', r.error
            } else if (!r.login)
                Ext.Msg.alert('Error', "A Server failure occured."
           
            Ext.callback(options.vpsCallback.failure, options.vpsCallback.scope, [response, options]
            retur
       
        options.vpsIsSuccess = tru

        try
            Ext.callback(options.vpsCallback.success, options.vpsCallback.scope, [response, options, r]
        } catch(e)
            Vps.handleError(e
       
    

    vpsFailure: function(response, option
   
        options.vpsIsSuccess = fals
        var debugString = '
        for (var dbg in options.params)
            debugString += '<br />params.' + dbg + ' = ' + options.params[dbg
       
        Ext.Msg.alert('Error', "A connection problem occured.<br /><br /><b>Debug info:</b><br /
            + "url: " + options.url + debugString
        Ext.callback(options.vpsCallback.failure, options.vpsCallback.scope, [response, options]
        retur
    
  
    vpsCallback: function(options, success, respons
   
        //wenn login-fenster angezeigt wird keinen callback aufrufen - weil der reque
        //wird ja erneut gesendet und da dann der callback aufgerufe
        if (options.vpsLogin) retur

        if (options.mask)
            Vps.Connection.masks-
            if (Vps.Connection.masks == 0)
                Ext.getBody().unmask(
                if (Ext.get('loading'))
                    Ext.get('loading').fadeOut({remove: true}
               
           
       

        if(success && !options.vpsIsSuccess)
            success = fals
       
        Ext.callback(options.vpsCallback.callback, options.vpsCallback.scope, [options, success, response]
   
}
Vps.Connection.masks = 0; //static var that hols number of masked reques

Ext.Ajax = new Vps.Connection
    /
     * The timeout in milliseconds to be used for requests. (defaul
     * to 3000
     * @type Numb
     * @property  timeo
     
    autoAbort : fals

    /
     * Serialize the passed form into a url encoded stri
     * @return {Strin
     
    serializeForm : function(form
        return Ext.lib.Ajax.serializeForm(form
   
}

