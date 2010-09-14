Ext.ux.ErrorHandler.on('error', function(ex) {
    // zeitweise kommt aus ein fehler von chrome://skype_ff_toolbar_win/content/injection_graph_func.js:1
    // der hier ignoriert wird. (falsche / nicht mehr verfügbare toolbar?)
    // gefunden bei 2F Stargate
    if (ex.url && ex.url.substr(0, 9) == 'chrome:/'+'/') {
        return;
    }
    if (Vps.Debug.displayErrors) {
        throw ex;
    }
    Ext.Ajax.request({
        url: '/vps/error/error/json-mail',
        ignoreErrors: true,
        params: {
            url: ex.url,
            lineNumber: ex.lineNumber,
            stack: Ext.encode(ex.stack),
            message: ex.message,
            location: location.href,
            referrer: document.referrer
        }
    });
});

if (!Vps.Debug.displayErrors) {
    Ext.ux.ErrorHandler.init();
}


Vps.handleError = function(error) {

    if (error instanceof String) error = { message: error };
    if (arguments[1]) error.title = arguments[1];
    if (arguments[2]) error.mail = arguments[2];
    if (!error.url) error.url = '';


    if ((error.checkRetry || Vps.Debug.displayErrors) && error.retry) {
        if (Vps.Debug.displayErrors) {
            title = error.title;
            msg = error.message;
        } else if (error.errorText) {
            title = error.errorText;
            msg = error.errorText;
        } else {
            title = (trlVps('Error'));
            msg = trlVps("A Server failure occured.");
            if (error.mail || (typeof error.mail == 'undefined')) {
                Ext.Ajax.request({
                    url: '/vps/error/error/json-mail',
                    params: {
                        url: error.url,
                        message: error.message,
                        location: location.href,
                        referrer: document.referrer
                    },
                    ignoreErrors: true
                });
            }
        }

            var win = new Ext.Window({
                    autoCreate : true,
                    title:title,
                    resizable:true,
                    constrain:true,
                    constrainHeader:true,
                    minimizable : false,
                    maximizable : false,
                    stateful: false,
                    modal: false,
                    shim:true,
                    buttonAlign:"center",
                    width:400,
                    minHeight: 300,
                    plain:true,
                    footer:true,
                    closable:false,
                    html: msg,
                    buttons: [{
                        text     : trlVps('Retry'),
                        handler  : function(){
                            error.retry.call(error.scope || window);
                            win.close();
                        }
                    },{
                        text     : trlVps('Abort'),
                        handler  : function(){
                            error.abort.call(error.scope || window);
                            win.close();
                        }
                }]

                });
                win.show();
    } else if (Vps.Debug.displayErrors) {
        Ext.Msg.show({
            title: error.title,
            msg: error.message,
            buttons: Ext.Msg.OK,
            modal: true,
            width: 800
        });
    } else {
        Ext.Msg.alert(trlVps('Error'), trlVps("A Server failure occured."));
        if (error.mail || (typeof error.mail == 'undefined')) {
            Ext.Ajax.request({
                url: '/vps/error/error/json-mail',
                params: {
                    url: error.url,
                    message: error.message,
                    location: location.href,
                    referrer: document.referrer
                }
            });
        }
    }
};
