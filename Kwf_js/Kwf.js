Ext.BLANK_IMAGE_URL = '/assets/ext/resources/images/default/s.gif';

Ext.namespace(
'Kwf', 'Kwc',
'Kwf.Component',
'Kwf.User.Login',
'Kwf.Auto',
'Kwf.Form',
'Kwf.Binding',
'Kwc.Advanced',
'Kwf.Debug',
'Kwf.Switch',
'Kwf.Basic.LinkTag.Extern',
'Kwf.Layout',
'Kwf.Utils'
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
Kwf.clone = function(o) {
    if('object' !== typeof o || o === null) {
        return o;
    }
    var c = 'function' === typeof o.pop ? [] : {};
    var p, v;
    for(p in o) {
        if(o.hasOwnProperty(p)) {
            v = o[p];
            if('object' === typeof v && v !== null) {
                c[p] = Kwf.clone(v);
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

Kwf.application = { maxAssetsMTime: '{$application.maxAssetsMTime}' };

//log das auch ohne irgendwelche abh�nigkeiten funktioniert (zB im Selenium)
Kwf.log = function(msg) {
    if (!Kwf.debugDiv) {
        Kwf.debugDiv = document.createElement('div');
        document.body.appendChild(Kwf.debugDiv);
        Kwf.debugDiv.style.position = 'absolute';
        Kwf.debugDiv.style.zIndex = '300';
        Kwf.debugDiv.style.top = 0;
        Kwf.debugDiv.style.right = 0;
        Kwf.debugDiv.style.backgroundColor = 'white';
        Kwf.debugDiv.style.fontSize = '10px';
    }
    Kwf.debugDiv.innerHTML += msg+'<br />';
};

//wird gesetzt in Kwf.Connection
Kwf.requestSentSinceLastKeepAlive = false;
Kwf.keepAlive = function() {
    if (!Kwf.requestSentSinceLastKeepAlive) {
        Ext.Ajax.request({
            url: '/kwf/user/login/json-keep-alive',
            ignoreErrors: true
        });
    } else {
        Kwf.requestSentSinceLastKeepAlive = false;
    }
    Kwf.keepAlive.defer(1000 * 60 * 5);
};

Kwf.keepAliveActivated = false;
Kwf.activateKeepAlive = function() {
    if (Kwf.keepAliveActivated) return;
    Kwf.keepAliveActivated = true;
    Kwf.keepAlive.defer(1000 * 60 * 5);
};

if (Kwf.isApp) {
    Kwf.activateKeepAlive();
}

Kwf.contentReadyHandlers = [];
Kwf.onContentReady = function(fn, scope) {
    Kwf.contentReadyHandlers.push({
        fn: fn,
        scope: scope
    });
};

Kwf.callOnContentReady = function(el, options) {
    if (!options) options = {};
    Ext.each(Kwf.contentReadyHandlers, function(i) {
        i.fn.call(i.scope || window, (el || document.body), options);
    }, this);
};

if (!Kwf.isApp) {
    Ext.onReady(function() {
        Kwf.callOnContentReady(document.body, { newRender: true });
    });
}

Kwf.include =  function(url, restart)
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
        if (restart) Kwf.restart();
    };
    document.getElementsByTagName("head")[0].appendChild(s);
};

Kwf.restart = function()
{
    Ext.getBody().unmask();
    if (Kwf.currentViewport) {
        Kwf.currentViewport.onDestroy();
        delete Kwf.currentViewport;
    }
    Kwf.main();
};

var restart = Kwf.restart;
var include = Kwf.include;
