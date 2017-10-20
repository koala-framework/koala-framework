Kwf.Debug.sentErrors = [];
Ext2.ux.ErrorHandler.on('error', function(ex) {
    var ownPrefix = location.protocol+'//'+location.host;
    if (ex.url && ex.url.substr(0, ownPrefix.length) != ownPrefix) {
        //ignore errors out of our control (other server, chrome://)
        return;
    }
    if (Kwf.Debug.displayErrors) {
        throw ex;
    }
    var params = {
        url: ex.url,
        lineNumber: ex.lineNumber,
        stack: Ext2.encode(ex.stack),
        message: ex.message,
        location: location.href,
        referrer: document.referrer
    };
    if (Kwf.Debug.sentErrors.indexOf(Ext2.encode(params)) != -1) {
        //this error has been sent alrady, don't send again
        return;
    }
    Kwf.Debug.sentErrors.push(Ext2.encode(params));
    Ext2.Ajax.request({
        url: KWF_BASE_URL+'/kwf/error/error/json-mail',
        ignoreErrors: true,
        params: params
    });
});

if (!Kwf.Debug.displayErrors) {
    Ext2.ux.ErrorHandler.init();
}

//called by Kwf.handleError to log the error
Kwf.ErrorHandler.log = function(error)
{
    Ext2.Ajax.request({
        url: KWF_BASE_URL+'/kwf/error/error/json-mail',
        params: {
            url: error.url,
            message: error.message,
            location: location.href,
            referrer: document.referrer
        },
        ignoreErrors: true
    });
};
