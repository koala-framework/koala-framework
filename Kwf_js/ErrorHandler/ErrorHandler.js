Ext2.ns('Kwf.ErrorHandler');

/**
 * message
 * title
 * mail bool
 * url
 * errorText nur für tests glaub ich, WTF?
 * checkRetry bool
 * mail bool
 * retry function
 * abort function
 */
Kwf.handleError = function(error) {

    if (typeof error == 'string') error = { message: error };
    if (arguments[1]) error.title = arguments[1];
    if (arguments[2]) error.mail = arguments[2];
    if (!error.url) error.url = '';


    if ((error.checkRetry || Kwf.Debug.displayErrors) && error.retry) {
        if (Kwf.Debug.displayErrors) {
            title = error.title;
            msg = error.message;
        } else if (error.errorText) {
            title = error.errorText;
            msg = error.errorText;
        } else {
            title = (trlKwf('Error'));
            msg = trlKwf("A Server failure occured.");
            if (error.mail || (typeof error.mail == 'undefined')) {
                Kwf.ErrorHandler.log({
                    url: error.url,
                    message: error.message
                });
            }
        }
        if (!Ext2.Window) {
            if (Kwf.Debug.displayErrors) {
                throw msg;
            }
        } else {
            var win = new Ext2.Window({
                autoCreate : true,
                title:title,
                resizable:true,
                constrain:true,
                constrainHeader:true,
                minimizable : false,
                maximizable : false,
                stateful: false,
                modal: true,
                shim:true,
                buttonAlign:"center",
                width:600,
                minHeight: 300,
                plain:true,
                footer:true,
                closable:false,
                html: msg,
                buttons: [{
                    text     : trlKwf('Retry'),
                    handler  : function(){
                        error.retry.call(error.scope || window);
                        win.close();
                    }
                },{
                    text     : trlKwf('Abort'),
                    handler  : function(){
                        error.abort.call(error.scope || window);
                        win.close();
                    }
                }]
            });
            win.show();
        }
    } else if (Kwf.Debug.displayErrors) {
        Ext2.Msg.show({
            title: error.title,
            msg: error.message,
            buttons: Ext2.Msg.OK,
            modal: true,
            width: 800
        });
        if (error.abort) error.abort.call(error.scope || window); //there is no possibility to retry, so just abort
    } else {
        Ext2.Msg.alert(trlKwf('Error'), trlKwf("A Server failure occured."));
        if (error.mail || (typeof error.mail == 'undefined')) {
            Kwf.ErrorHandler.log({
                url: error.url,
                message: error.message
            });
        }
        if (error.abort) error.abort.call(error.scope || window); //there is no possibility to retry, so just abort
    }
};
