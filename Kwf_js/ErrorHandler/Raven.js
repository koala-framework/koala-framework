if (!Kwf.Debug.displayErrors) {
    Raven.config(Kwf.RavenJsConfig.dsn, {
        dataCallback: function(data)
        {
            if (Kwf.user) {
                data.user = Kwf.user;
            }
            return data;
        },
        ignoreUrls: [
        // Facebook flakiness
        /graph\.facebook\.com/i,
        // Facebook blocked
        /connect\.facebook\.net\/en_US\/all\.js/i,
        // Woopra flakiness
        /eatdifferent\.com\.woopra-ns\.com/i,
        /static\.woopra\.com\/js\/woopra\.js/i,
        // Chrome extensions
        /extensions\//i,
        /^chrome:\/\//i,
        // Other plugins
        /127\.0\.0\.1:4001\/isrunning/i,  // Cacaoweb
        /webappstoolbarba\.texthelp\.com\//i,
        /metrics\.itunes\.apple\.com\.edgesuite\.net\//i
        ]
    }).install();

    //called by Kwf.handleError to log the error
    Kwf.ErrorHandler.log = function(error)
    {
        Raven.captureMessage(error.message, {extra: { url: error.url }});
    };
} else {

    Kwf.ErrorHandler.log = function(error)
    {
        //noop
    }
}
