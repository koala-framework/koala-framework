Ext2.namespace('Kwf.Facebook');
Kwf.FacebookClass = function() {
    this.addEvents('afterinit'); //deprecated
    this._onReadyCallbacks = [];
};
Ext2.extend(Kwf.FacebookClass, Ext2.util.Observable, {
    loadedAppId: null,
    isReady: false,
    onReady: function(options) {
        if (!options.callback) {
            options = {
                callback: options,
                scope: arguments[1] || this
            };
        }
        if (!options.appId) {
            throw new Error('No appId config given');
        }

        if (!this.loadedAppId) {
            this.loadedAppId = options.appId;

            var self = this;
            window.fbAsyncInit = function() {
                FB.init({
                    appId      : options.appId, // App ID
                    status     : true, // check login status
                    cookie     : true, // enable cookies to allow the server to access the session
                    oauth      : true, // enable OAuth 2.0
                    xfbml      : true  // parse XFBML
                });

                self.isReady = true;
                self.fireEvent('afterinit'); //deprecated
                self._onReadyCallbacks.each(function(i) {
                    i.callback.call(i.callback.scope || window);
                });
            };

            // Load the SDK Asynchronously
            (function(d){
                var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
                js = d.createElement('script'); js.id = id; js.async = true;
                js.src = "//connect.facebook.net/" + trlcKwf('facebook js-sdk locale', "en_US") + "/all.js";
                d.getElementsByTagName('head')[0].appendChild(js);
            }(document));
        } else {
            if (this.loadedAppId != options.appId) {
                throw new Error('Facebook API can only be called with same the appId per page');
            }
        }

        if (this.isReady) {
            options.callback.call(options.scope || window);
        } else {
            this._onReadyCallbacks.push({ callback: options.callback, scope: options.scope });
        }
    }
});
Kwf.Facebook = new Kwf.FacebookClass();
