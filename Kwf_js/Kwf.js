Ext2.BLANK_IMAGE_URL = '/assets/ext2/resources/images/default/s.gif';

if(!Kwf) Kwf = {};

Kwf.namespace = function() {
    var a=arguments, o=null, i, j, d, rt;
    for (i=0; i<a.length; ++i) {
        d=a[i].split(".");
        rt = d[0];
        eval('if (typeof ' + rt + ' == "undefined"){' + rt + ' = {};} o = ' + rt + ';');
        for (j=1; j<d.length; ++j) {
            o[d[j]]=o[d[j]] || {};
            o=o[d[j]];
        }
    }
}

Kwf.namespace(
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


Ext2.applyIf(Array.prototype, {

    //deprecated! -> forEach (ist auch ein JS-Standard!)
    each : function(fn, scope){
        Ext2.each(this, fn, scope);
    },

    //to use array.forEach directly
    forEach : function(fn, scope){
        Ext2.each(this, fn, scope);
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

Ext2.applyIf(Function.prototype, {

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
            Ext2.apply(interception.prototype, this.prototype);
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

if (!Ext2.isObject) {
    //TODO Ext4: remove
    Ext2.isObject = (Ext2.toString.call(null) === '[object Object]') ?
    function(value) {
        // check ownerDocument here as well to exclude DOM nodes
        return value !== null && value !== undefined && Ext2.toString.call(value) === '[object Object]' && value.ownerDocument === undefined;
    } :
    function(value) {
        return Ext2.toString.call(value) === '[object Object]';
    };
}

Ext2.onReady(function()
{
//     Ext2.state.Manager.setProvider(new Ext2.state.CookieProvider());

    if (Ext2.QuickTips) {
        Ext2.QuickTips.init();
    }

    if (Ext2.isIE6) {
        Ext2.each(Ext2.DomQuery.select('.addHover'), function(el) {
            var extEl = Ext2.fly(el);
            extEl.hover(
                function() { this.addClass('hover'); },
                function() { this.removeClass('hover'); },
                extEl
            );
        });
    }
});


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

//set in Kwf.Connection
Kwf.requestSentSinceLastKeepAlive = false;

Kwf.keepAlive = function() { //can be overridden
    Ext2.Ajax.request({
        url: '/kwf/user/login/json-keep-alive',
        ignoreErrors: true
    });
};

Kwf._keepAlive = function() {
    if (!Kwf.requestSentSinceLastKeepAlive) {
        Kwf.keepAlive();
    } else {
        Kwf.requestSentSinceLastKeepAlive = false;
    }
    Kwf._keepAlive.defer(1000 * 60 * 5);
};

Kwf._keepAliveActivated = false;
Kwf.activateKeepAlive = function() {
    if (Kwf._keepAliveActivated) return;
    Kwf._keepAliveActivated = true;
    Kwf._keepAlive.defer(1000 * 60 * 5);
};

if (Kwf.isApp) {
    Kwf.activateKeepAlive();
}

Kwf._componentEventHandlers = {};
/**
 * Fires a component event, used in frontend.
 *
 * @param string event name
 * @param parameters[...] pass to event handler
 */
Kwf.fireComponentEvent = function(evName) {
    if (Kwf._componentEventHandlers[evName]) {
        var args = [];
        for (var i=1; i<arguments.length; i++) { //remove first
            args.push(arguments[i]);
        }
        Kwf._componentEventHandlers[evName].forEach(function(i) {
            i.cb.apply(i.scope || window, args);
        }, this);
    }
};

/**
 * Adds event listener to a component event, used in frontend.
 *
 * @param string event name
 * @param callback function
 * @param scope
 */
Kwf.onComponentEvent = function(evName, cb, scope) {
    if (!Kwf._componentEventHandlers[evName]) Kwf._componentEventHandlers[evName] = [];
    Kwf._componentEventHandlers[evName].push({
        cb: cb,
        scope: scope
    });
};

Kwf.getKwcRenderUrl = function() {
    var url = '/kwf/util/kwc/render';
    if (Kwf.Debug.rootFilename) url = Kwf.Debug.rootFilename + url;
    if (location.search.match(/[\?&]kwcPreview/)) url += '?kwcPreview';
    return url;
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
    Ext2.getBody().unmask();
    if (Kwf.currentViewport) {
        Kwf.currentViewport.onDestroy();
        delete Kwf.currentViewport;
    }
    Kwf.main();
};

var restart = Kwf.restart;
var include = Kwf.include;
