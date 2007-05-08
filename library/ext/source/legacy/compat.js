/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


// backwards compat
YAHOO.ext = Ext;

YAHOO.extendX = Ext.extend;
YAHOO.namespaceX = Ext.namespace;

Ext.Strict = Ext.isStrict;

Ext.util.Config = {};
Ext.util.Config.apply = Ext.apply;

// this is nasty
Ext.util.Browser = Ext;

// removed
YAHOO.override = Ext.override;


 /*
 * Enable custom handler signature and event cancelling. Using fireDirect() instead of fire() calls the subscribed event handlers
 * with the exact parameters passed to fireDirect, instead of the usual (eventType, args[], obj). IMO this is more intuitive
 * and promotes cleaner code. Also, if an event handler returns false, it is returned by fireDirect and no other handlers will be called.<br>
 * Example:<br><br><pre><code>
 * if(beforeUpdateEvent.fireDirect(myArg, myArg2) !== false){
 *     // do update
 * }</code></pre>
 */
YAHOO.util.CustomEvent.prototype.fireDirect = function(){
    var len=this.subscribers.length;
    for (var i=0; i<len; ++i) {
        var s = this.subscribers[i];
        if(s){
            var scope = (s.override) ? s.obj : this.scope;
            if(s.fn.apply(scope, arguments) === false){
                return false;
            }
        }
    }
    return true;
};

Ext.apply(Ext.util.Observable.prototype, {
    delayedListener : function(eventName, fn, scope, delay){
        return this.addListener(eventName, fn, {scope: scope, delay: delay || 10});
    },

    bufferedListener : function(eventName, fn, scope, millis){
        return this.addListener(eventName, fn, {scope: scope, buffer: millis || 250});
    }
});

Ext.apply(Ext.Element.prototype, {

    // replaced with more powerful selector functions
    /**
    * Gets an array of child Ext.Element objects by tag name
    * @param {String} tagName
    * @return {Array} The children
    */
    getChildrenByTagName : function(tagName){
        var children = this.dom.getElementsByTagName(tagName);
        var len = children.length;
        var ce = new Array(len);
        for(var i = 0; i < len; ++i){
            ce[i] = El.get(children[i], true);
        }
        return ce;
    },

    /**
    * Gets an array of child Ext.Element objects by class name and optional tagName
    * @param {String} className
    * @param {String} tagName (optional)
    * @return {Array} The children
    */
    getChildrenByClassName : function(className, tagName){
        var children = D.getElementsByClassName(className, tagName, this.dom);
        var len = children.length;
        var ce = new Array(len);
        for(var i = 0; i < len; ++i){
            ce[i] = El.get(children[i], true);
        }
        return ce;
    },

    // these 2 where replaced by "position()"
    /**
    * Set the element as absolute positioned with the specified z-index
    * @param {Number} zIndex (optional)
    * @return {Ext.Element} this
     */
    setAbsolutePositioned : function(zIndex){
        this.setStyle("position", "absolute");
        if(zIndex){
            this.setStyle("z-index", zIndex);
        }
        return this;
    },

    /**
    * Set the element as relative positioned with the specified z-index
    * @param {Number} zIndex (optional)
    * @return {Ext.Element} this
     */
    setRelativePositioned : function(zIndex){
        this.setStyle("position", "relative");
        if(zIndex){
            this.setStyle("z-index", zIndex);
        }
        return this;
    },

    // replaced by new Event system

    bufferedListener : function(eventName, fn, scope, millis){
        return this.on(eventName, fn, scope || this, {buffer: millis || 250});
    },


    addHandler : function(eventName, stopPropagation, handler, scope, override){
        return this.on(eventName, fn, scope || this, {stopPropagation: stopPropagation, preventDefault: true});
    },

    addManagedListener : function(eventName, fn, scope, override){
        return Ext.EventManager.on(this.dom, eventName, fn, scope || this);
    }
});

// replaced by more advanced getTarget()
Ext.EventObject.findTarget = function(className, tagName){
    if(tagName) tagName = tagName.toLowerCase();
    if(this.browserEvent){
        function isMatch(el){
            if(!el){
                return false;
            }
            if(className && !D.hasClass(el, className)){
                return false;
            }
            return !(tagName && el.tagName.toLowerCase() != tagName);

        };

        var t = this.getTarget();
        if(!t || isMatch(t)){
            return t;
        }
        var p = t.parentNode;
        var b = document.body;
        while(p && p != b){
            if(isMatch(p)){
                return p;
            }
            p = p.parentNode;
        }
    }
    return null;
};

