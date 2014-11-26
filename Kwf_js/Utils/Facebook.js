Ext2.namespace('Kwf.Facebook');
Kwf.FacebookClass = function() {
    this.addEvents('afterinit'); //deprecated
    this._onReadyCallbacks = [];
};
Ext2.extend(Kwf.FacebookClass, Ext2.util.Observable, {
    loadedSubroot: null,
    isReady: false,
    onReady: function(options) {
        if (!options.callback) {
            options = {
                callback: options,
                scope: arguments[1] || this
            };
        }
        if (!options.subroot) options.subroot = 'root';

        if (!Kwf.FacebookAppIds[options.subroot]) {
            throw new Error('No fbAppData.appId base property set for '+options.subroot);
        }

        if (!this.loadedSubroot) {
            this.loadedSubroot = options.subroot;

            var self = this;
            window.fbAsyncInit = function() {
                FB.init({
                    appId      : Kwf.FacebookAppIds[options.subroot], // App ID
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
            if (this.loadedSubroot != options.subroot) {
                throw new Error('Facebook API can only be called with same the subroot per page');
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
