Ext.namespace('Kwf.Facebook');
Kwf.Facebook.appId = '{Kwf_Util_Facebook_Assets::getAppId()}';
window.fbAsyncInit = function() {
    FB.init({
        appId      : Kwf.Facebook.appId, // App ID
        status     : true, // check login status
        cookie     : true, // enable cookies to allow the server to access the session
        oauth      : true, // enable OAuth 2.0
        xfbml      : true  // parse XFBML
    });
// Additional initialization code here
};
// Load the SDK Asynchronously
(function(d){
    var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
    js = d.createElement('script'); js.id = id; js.async = true;
    js.src = "//connect.facebook.net/" + trlcKwf('facebook js-sdk locale', "en_US") + "/all.js";
    d.getElementsByTagName('head')[0].appendChild(js);
}(document));
