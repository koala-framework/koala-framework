Ext.BLANK_IMAGE_URL = '/assets/ext/resources/images/default/s.gif';

Ext.namespace(
'Vps', 'Vpc',
'Vps.Component',
'Vps.User.Login',
'Vps.Auto',
'Vps.Form',
'Vps.Binding',
'Vpc.Advanced',
'Vps.Debug',
'Vps.Switch',
'Vps.Basic.LinkTag.Extern',
'Vps.Layout',
'Vps.Utils'
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
    },

    //+ Jonas Raoni Soares Silva
    //@ http://jsfromhell.com/array/shuffle [rev. #1]
    shuffle : function() {
        for(var j, x, i = this.length; i; j = parseInt(Math.random() * i), x = this[--i], this[i] = this[j], this[j] = x);
        return this;
    }
});

Ext.applyIf(Function.prototype, {

    interceptResult: function(fcn, scope) {
        if(typeof fcn != "function"){
            return this;
        }
        var method = this;
        var interception=function() {
            var retval = method.apply(this || window, arguments);
            var callArgs = Array.prototype.slice.call(arguments, 0);
            var args=[retval].concat(callArgs);
            var newRetval=fcn.apply(scope || this || window, args);
            return newRetval;
        };
        if (this.prototype){
            Ext.apply(interception.prototype, this.prototype);
            if (this.superclass){ interception.superclass=this.superclass; }
            if (this.override){ interception.override=this.override; }
        }
        return interception;
    }
});

//http://extjs.com/forum/showthread.php?t=26644
Vps.clone = function(o) {
    if('object' !== typeof o || o === null) {
        return o;
    }
    var c = 'function' === typeof o.pop ? [] : {};
    var p, v;
    for(p in o) {
        if(o.hasOwnProperty(p)) {
            v = o[p];
            if('object' === typeof v && v !== null) {
                c[p] = Vps.clone(v);
            }
            else {
                c[p] = v;
            }
        }
    }
    return c;
};

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

Vps.application = { maxAssetsMTime: '{$application.maxAssetsMTime}' };

//log das auch ohne irgendwelche abh�nigkeiten funktioniert (zB im Selenium)
Vps.log = function(msg) {
    if (!Vps.debugDiv) {
        Vps.debugDiv = document.createElement('div');
        document.body.appendChild(Vps.debugDiv);
        Vps.debugDiv.style.position = 'absolute';
        Vps.debugDiv.style.zIndex = '300';
        Vps.debugDiv.style.top = 0;
        Vps.debugDiv.style.right = 0;
        Vps.debugDiv.style.backgroundColor = 'white';
        Vps.debugDiv.style.fontSize = '10px';
    }
    Vps.debugDiv.innerHTML += msg+'<br />';
};

//wird gesetzt in Vps.Connection
Vps.requestSentSinceLastKeepAlive = false;
Vps.keepAlive = function() {
    if (!Vps.requestSentSinceLastKeepAlive) {
        Ext.Ajax.request({
            url: '/vps/user/login/json-keep-alive',
            ignoreErrors: true
        });
    } else {
        Vps.requestSentSinceLastKeepAlive = false;
    }
    Vps.keepAlive.defer(1000 * 60 * 5);
};

Vps.keepAliveActivated = false;
Vps.activateKeepAlive = function() {
    if (Vps.keepAliveActivated) return;
    Vps.keepAliveActivated = true;
    Vps.keepAlive.defer(1000 * 60 * 5);
};

if (Vps.isApp) {
    Vps.activateKeepAlive();
}

Vps.contentReadyHandlers = [];
Vps.onContentReady = function(fn, scope) {
    if (Vps.isApp) {
        //in einer Ext-Anwendung mit Vps.main den contentReadHandler
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
        s.setAttribute('rel', 'stylesheet');
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
