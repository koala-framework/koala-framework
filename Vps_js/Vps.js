Ext.BLANK_IMAGE_URL = '/assets/ext/resources/images/default/s.gif';

Ext.namespace(
'Vps', 'Vpc',
'Vps.Component',
'Vps.User.Login',
'Vps.Auto',
'Vps.Form',
'Vps.Binding',
'Vpc.Advanced'
);

Ext.applyIf(Array.prototype, {

    //deprecated! -> forEach (ist auch ein JS-Standard!)
    each : function(fn, scope){
        Ext.each(this, fn, scope);
    },

    //to use array.forEach directly
    forEach : function(fn, scope){
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
            Ext.Msg.alert(trlVps('Error'), trlVps("An error occured"));
        }
        Ext.Ajax.request({
            url: '/vps/error/error/jsonMail',
            params: {msg: e}
        });
    }
};

Vps.contentReadyHandlers = [];
Vps.onContentReady = function(fn, scope) {
    if (Vps.currentViewport) {
        //in einer Ext-Anwendung mit viewport den contentReadHandler
        //nicht gleich ausführen, das paragraphs-panel führt es dafür aus
        Vps.contentReadyHandlers.push({
            fn: fn,
            scope: scope
        });
    } else {
        //normales Frontend
        Ext.onReady(fn, scope);
    }
};

Vps.include =  function(url, restart)
{
    if (url.substr(-4) == '.css') {
        var s = document.createElement('link');
        s.setAttribute('type', 'text/css');
        s.setAttribute('href', url+'?'+Math.random());
    } else {
        var s = document.createElement('script');
        s.setAttribute('type', 'text/javascript');
        s.setAttribute('src', url+'?'+Math.random());
    }
    s.onload = function () {
        if (restart) Vps.restart();
    };
    document.getElementsByTagName("head")[0].appendChild(s);
};

Vps.restart = function()
{
    Ext.getBody().unmask();
    if (Vps.currentViewport) {
        Vps.currentViewport.onDestroy();
        delete Vps.currentViewport;
    }
    Vps.main();
};

var restart = Vps.restart;
var include = Vps.include;
