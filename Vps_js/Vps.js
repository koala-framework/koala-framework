Ext.BLANK_IMAGE_URL = '/assets/ext/resources/images/default/s.gif';

Ext.namespace(
'Vps', 'Vpc',
'Vps.Component',
'Vps.User.Login',
'Vps.Auto',
'Vps.Form'
);

Ext.applyIf(Array.prototype, {

    //to use array.each directly
    each : function(fn, scope){
        Ext.each(this, fn, scope);
    },

    //add is alias for push
    add : function() {
        this.push.apply(this, arguments);
    }
});

Ext.onReady(function()
{
//     Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

    if (Ext.QuickTips) {
        Ext.QuickTips.init();
    }

    if (Ext.isIE6) {
        Ext.each(Ext.DomQuery.select('.addHover'), function(el) {
            var extEl = Ext.fly(el);
            extEl.hover(
                function() { this.addClass('hover'); },
                function() { this.removeClass('hover'); },
                extEl
            );
        });
    }
});

Vps.application = { version: '{$application.version}' };

Vps.callWithErrorHandler = function(fn, scope) {
    if (Vps.debug) {
        //call without error handler
        return fn.call(scope || window);
    }
    try {
        return fn.call(scope || window);
    } catch(e) {
        if (e.toString) e = e.toString();
        if (e.message) e = e.message;
        if(Ext.get('loading')) {
            Ext.get('loading').fadeOut({remove: true});
        }
        if (Ext.Msg) {
            Ext.Msg.alert('Error', "Ein Fehler ist aufgetreten.");
        }
        Ext.Ajax.request({
            url: '/vps/error/error/jsonMail',
            params: {msg: e}
        });
    }
};


Vps.include =  function(url, restart) {
    Ext.Ajax.request({
        url: url,
        success: function(response, options)  {
            s = document.createElement('script');
            s.setAttribute('type', 'text/javascript');
            s.appendChild(document.createTextNode(response.responseText));
            document.getElementsByTagName("head")[0].appendChild(s);
            if (restart) Vps.restart();
        }
    });
};
Vps.restart = function() {
    if (Vps.currentViewport) {
        Vps.currentViewport.onDestroy();
        delete Vps.currentViewport;
    }
    Vps.main();
};

var restart = Vps.restart;
var include = Vps.include;