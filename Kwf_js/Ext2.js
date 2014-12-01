Ext2.BLANK_IMAGE_URL = '/assets/ext2/resources/images/default/s.gif';


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
