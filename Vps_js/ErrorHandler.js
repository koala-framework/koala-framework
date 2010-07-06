Ext.ux.ErrorHandler.on('error', function(ex) {
    if (ex.url && ex.url.substr(0, 8) == 'chrome:/') {
        return;
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
