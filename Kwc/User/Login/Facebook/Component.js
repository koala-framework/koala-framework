Kwf.onElementReady('.kwcUserLoginFacebook', function(el, config){
    if (!Ext.get('fb-root')) {
        el.createChild({id: 'fb-root'});
    }
    Kwf.Facebook.on('afterinit', function(){
        el.child('.kwfFbLoginButton').on('click', function(ev){
            loginInit();
        }, this);
    }, this);
    var loginInit = function() {
        FB.getLoginStatus(function(response){
            if (response.status === 'connected') {
                Ext.Ajax.request({
                    params: {
                        accessToken: response.authResponse.accessToken
                    },
                    url: config.controllerUrl + '/json-auth',
                    success: function(response, options, r) {
                        el.child('.success').show();
                        Kwf.callOnContentReady(el.dom);
                    },
                    scope: this
                });
        } else {
            FB.login(function(response) {
                // handle the response
                loginInit();
            }, {scope: 'email'});
        }
    });
    }
}, this);