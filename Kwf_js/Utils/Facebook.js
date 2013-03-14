Ext.namespace('Kwf.Facebook');
Kwf.FacebookClass = function() {
    this.addEvents('afterinit'); //deprecated
    this._onReadyCallbacks = [];
};
Ext.extend(Kwf.FacebookClass, Ext.util.Observable, {
    isReady: false,
    onReady: function(callback, scope) {
        if (this.isReady) {
            callback.call(scope || window);
        } else {
            this._onReadyCallbacks.push({ callback: callback, scope: scope });
        }
    }
});
Kwf.Facebook = new Kwf.FacebookClass();
Kwf.Facebook.appId = '{Kwf_Util_Facebook_Assets::getAppId()}';
window.fbAsyncInit = function() {
    FB.init({
        appId      : Kwf.Facebook.appId, // App ID
        status     : true, // check login status
        cookie     : true, // enable cookies to allow the server to access the session
        oauth      : true, // enable OAuth 2.0
        xfbml      : true  // parse XFBML
    });
    Kwf.Facebook.isReady = true;
    Kwf.Facebook.fireEvent('afterinit'); //deprecated
    Kwf.Facebook._onReadyCallbacks.each(function(i) {
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
