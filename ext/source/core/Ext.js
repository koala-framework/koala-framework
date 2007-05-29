/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext = {};

// for old browsers
window["undefined"] = window["undefined"];

/**
 * @class Ext
 * Ext core utilties and functions
 * @singleton
 */

/**
 * Copies all the properties of config to obj.
 * @param {Object} obj The receiver of the properties
 * @param {Object} config The source of the properties
 * @param {Object} defaults A different object that will also be applied for default values
 * @return {Object} returns obj
 * @member Ext apply
 */
Ext.apply = function(o, c, defaults){
    if(defaults){
        // no "this" reference for friendly out of scope calls
        Ext.apply(o, defaults);
    }
    if(o && c && typeof c == 'object'){
        for(var p in c){
            o[p] = c[p];
        }
    }
    return o;
};

(function(){
    var idSeed = 0;
    var ua = navigator.userAgent.toLowerCase();

    var isStrict = document.compatMode == "CSS1Compat",
        isOpera = ua.indexOf("opera") > -1,
        isSafari = (/webkit|khtml/).test(ua),
        isIE = ua.indexOf("msie") > -1,
        isIE7 = ua.indexOf("msie 7") > -1,
        isGecko = !isSafari && ua.indexOf("gecko") > -1,
        isBorderBox = isIE && !isStrict,
        isWindows = (ua.indexOf("windows") != -1 || ua.indexOf("win32") != -1),
        isMac = (ua.indexOf("macintosh") != -1 || ua.indexOf("mac os x") != -1),
        isSecure = window.location.href.toLowerCase().indexOf("https") === 0;

    // remove css image flicker
	if(isIE && !isIE7){
        try{
            document.execCommand("BackgroundImageCache", false, true);
        }catch(e){}
    }

    Ext.apply(Ext, {
        /**
         * True if the browser is in strict mode
         * @type Boolean
         */
        isStrict : isStrict,
        /**
         * True if the page is running over SSL
         * @type Boolean
         */
        isSecure : isSecure,
        /**
         * True when the document is fully initialized and ready for action
         * @type Boolean
         */
        isReady : false,
        /**
         * URL to a blank file used by Ext when in secure mode for iframe src and onReady src to prevent
         * the IE insecure content warning (defaults to javascript:false).
         * @type String
         */
        SSL_SECURE_URL : "javascript:false",

        /**
         * URL to a 1x1 transparent gif image used by Ext to create inline icons with CSS background images. (Defaults to
         * "http://extjs.com/s.gif" and you should change this to a URL on your server).
         * @type String
         */
        BLANK_IMAGE_URL : "http:/"+"/extjs.com/s.gif",

        emptyFn : function(){},

        /**
         * Copies all the properties of config to obj if they don't already exist.
         * @param {Object} obj The receiver of the properties
         * @param {Object} config The source of the properties
         * @return {Object} returns obj
         */
        applyIf : function(o, c){
            if(o && c){
                for(var p in c){
                    if(typeof o[p] == "undefined"){ o[p] = c[p]; }
                }
            }
            return o;
        },

        /**
         * Applies event listeners to elements by selectors when the document is ready.
         * The event name is specified with an @ suffix.
<pre><code>
Ext.addBehaviors({
   // add a listener for click on all anchors in element with id foo
   '#foo a@click' : function(e, t){
       // do something
   },

   // add the same listener to multiple selectors (separated by comma BEFORE the @)
   '#foo a, #bar span.some-class@mouseover' : function(){
       // do something
   }
});
</code></pre>
         * @param {Object} obj The list of behaviors to apply
         */
        addBehaviors : function(o){
            if(!Ext.isReady){
                Ext.onReady(function(){
                    Ext.addBehaviors(o);
                });
                return;
            }
            var cache = {}; // simple cache for applying multiple behaviors to same selector does query multiple times
            for(var b in o){
                var parts = b.split('@');
                if(parts[1]){ // for Object prototype breakers
                    var s = parts[0];
                    if(!cache[s]){
                        cache[s] = Ext.select(s);
                    }
                    cache[s].on(parts[1], o[b]);
                }
            }
            cache = null;
        },

        /**
         * Generates unique ids. If the element already has an id, it is unchanged
         * @param {String/HTMLElement/Element} el (optional) The element to generate an id for
         * @param {String} prefix (optional) Id prefix (defaults "ext-gen")
         */
        id : function(el, prefix){
            prefix = prefix || "ext-gen";
            el = Ext.getDom(el);
            var id = prefix + (++idSeed);
            return el ? (el.id ? el.id : (el.id = id)) : id;
        },

        /**
         * Extends one class with another class and optionally overrides members with the passed literal. This class
         * also adds the function "override()" to the class that can be used to override
         * members on an instance.
         * @param {Object} subclass The class inheriting the functionality
         * @param {Object} superclass The class being extended
         * @param {Object} overrides (optional) A literal with members
         * @method extend
         */
        extend : function(){
            // inline overrides
            var io = function(o){
                for(var m in o){
                    this[m] = o[m];
                }
            };
            return function(sb, sp, overrides){
                if(typeof sp == 'object'){
                    overrides = sp;
                    sp = sb;
                    sb = function(){sp.apply(this, arguments);};
                }
                var F = function(){}, sbp, spp = sp.prototype;
                F.prototype = spp;
                sbp = sb.prototype = new F();
                sbp.constructor=sb;
                sb.superclass=spp;
                if(spp.constructor == Object.prototype.constructor){
                    spp.constructor=sp;
                }
                sb.override = function(o){
                    Ext.override(sb, o);
                };
                sbp.override = io;
                sbp.__extcls = sb;
                Ext.override(sb, overrides);
                return sb;
            };
        }(),

        override : function(origclass, overrides){
            if(overrides){
                var p = origclass.prototype;
                for(var method in overrides){
                    p[method] = overrides[method];
                }
            }
        },
        /**
         * Creates namespaces but does not assume YAHOO is the root.
         * @param {String} namespace1
         * @param {String} namespace2
         * @param {String} etc
         * @method namespace
         */
        namespace : function(){
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
        },

        /**
         * Takes an object and converts it to an encoded URL. e.g. Ext.urlEncode({foo: 1, bar: 2}); would return "foo=1&bar=2".  Optionally, property values can be arrays, instead of keys and the resulting string that's returned will contain a name/value pair for each array value.
         * @param {Object} o
         * @return {String}
         */
        urlEncode : function(o){
            if(!o){
                return "";
            }
            var buf = [];
            for(var key in o){
                var ov = o[key];
                var type = typeof ov;
                if(type == 'undefined'){
                    buf.push(encodeURIComponent(key), "=&");
                }else if(type != "function" && type != "object"){
                    buf.push(encodeURIComponent(key), "=", encodeURIComponent(ov), "&");
                }else if(ov instanceof Array){
                    for(var i = 0, len = ov.length; i < len; i++) {
                        buf.push(encodeURIComponent(key), "=", encodeURIComponent(ov[i] === undefined ? '' : ov[i]), "&");
                    }
                }
            }
            buf.pop();
            return buf.join("");
        },

        /**
         * Takes an encoded URL and and converts it to an object. e.g. Ext.urlDecode("foo=1&bar=2"); would return {foo: 1, bar: 2} or Ext.urlDecode("foo=1&bar=2&bar=3&bar=4", true); would return {foo: 1, bar: [2, 3, 4]}.
         * @param {String} string
         * @param {Boolean} overwrite (optional) Items of the same name will overwrite previous values instead of creating an an array (Defaults to false).
         * @return {Object} A literal with members
         */
        urlDecode : function(string, overwrite){
            if(!string || !string.length){
                return {};
            }
            var obj = {};
            var pairs = string.split('&');
            var pair, name, value;
            for(var i = 0, len = pairs.length; i < len; i++){
                pair = pairs[i].split('=');
                name = decodeURIComponent(pair[0]);
                value = decodeURIComponent(pair[1]);
                if(overwrite !== true){
                    if(typeof obj[name] == "undefined"){
                        obj[name] = value;
                    }else if(typeof obj[name] == "string"){
                        obj[name] = [obj[name]];
                        obj[name].push(value);
                    }else{
                        obj[name].push(value);
                    }
                }else{
                    obj[name] = value;
                }
            }
            return obj;
        },

        /**
         * Iterates an array calling the passed function with each item, stopping if your function returns false. If the
         * passed array is not really an array, your function is called once with it.
         * The supplied function is called with (Object item, Number index, Array allItems).
         * @param {Array/NodeList/Mixed} array
         * @param {Function} fn
         * @param {Object} scope
         */
        each : function(array, fn, scope){
            if(typeof array.length == "undefined" || typeof array == "string"){
                array = [array];
            }
            for(var i = 0, len = array.length; i < len; i++){
                if(fn.call(scope || array[i], array[i], i, array) === false){ return i; };
            }
        },

        // deprecated
        combine : function(){
            var as = arguments, l = as.length, r = [];
            for(var i = 0; i < l; i++){
                var a = as[i];
                if(a instanceof Array){
                    r = r.concat(a);
                }else if(a.length !== undefined && !a.substr){
                    r = r.concat(Array.prototype.slice.call(a, 0));
                }else{
                    r.push(a);
                }
            }
            return r;
        },

        /**
         * Escapes the passed string for use in a regular expression
         * @param {String} str
         * @return {String}
         */
        escapeRe : function(s) {
            return s.replace(/([.*+?^${}()|[\]\/\\])/g, "\\$1");
        },

        // internal
        callback : function(cb, scope, args, delay){
            if(typeof cb == "function"){
                if(delay){
                    cb.defer(delay, scope, args || []);
                }else{
                    cb.apply(scope, args || []);
                }
            }
        },

        /**
         * Return the dom node for the passed string (id), dom node, or Ext.Element
         * @param {String/HTMLElement/Element) el
         * @return HTMLElement
         */
        getDom : function(el){
            if(!el){
                return null;
            }
            return el.dom ? el.dom : (typeof el == 'string' ? document.getElementById(el) : el);
        },

        /**
        * Shorthand for {@link Ext.ComponentMgr#get}
        * @param {String} id
        * @return Ext.Component
        */
        getCmp : function(id){
            return Ext.ComponentMgr.get(id);
        },

        num : function(v, defaultValue){
            if(typeof v != 'number'){
                return defaultValue;
            }
            return v;
        },

        destroy : function(){
            for(var i = 0, a = arguments, len = a.length; i < len; i++) {
                var as = a[i];
                if(as){
                    if(as.dom){
                        as.removeAllListeners();
                        as.remove();
                        continue;
                    }
                    if(typeof as.purgeListeners == 'function'){
                        as.purgeListeners();
                    }
                    if(typeof as.destroy == 'function'){
                        as.destroy();
                    }
                }
            }
        },

        /* @type Boolean */
        isOpera : isOpera,
        /* @type Boolean */
        isSafari : isSafari,
        /* @type Boolean */
        isIE : isIE,
        /* @type Boolean */
        isIE7 : isIE7,
        /* @type Boolean */
        isGecko : isGecko,
        /* @type Boolean */
        isBorderBox : isBorderBox,
        /* @type Boolean */
        isWindows : isWindows,
        /* @type Boolean */
        isMac : isMac,

    /**
     By default, Ext intelligently decides whether floating elements should be shimmed. If you are using flash,
     you may want to set this to true.
     @type Boolean
     */
        useShims : ((isIE && !isIE7) || (isGecko && isMac))
    });


})();

Ext.namespace("Ext", "Ext.util", "Ext.grid", "Ext.dd", "Ext.tree", "Ext.data",
                "Ext.form", "Ext.menu", "Ext.state", "Ext.lib", "Ext.layout");


/**
 * @class Function
 * These functions are available on every Function object (any javascript function).
 */
Ext.apply(Function.prototype, {
     /**
     * Creates a callback that passes arguments[0], arguments[1], arguments[2], ...
     * Call directly on any function. Example: <code>myFunction.createCallback(myarg, myarg2)</code>
     * Will create a function that is bound to those 2 args.
     * @return {Function} The new function
    */
    createCallback : function(/*args...*/){
        // make args available, in function below
        var args = arguments;
        var method = this;
        return function() {
            return method.apply(window, args);
        };
    },

    /**
     * Creates a delegate (callback) that sets the scope to obj.
     * Call directly on any function. Example: <code>this.myFunction.createDelegate(this)</code>
     * Will create a function that is automatically scoped to this.
     * @param {Object} obj (optional) The object for which the scope is set
     * @param {Array} args (optional) Overrides arguments for the call. (Defaults to the arguments passed by the caller)
     * @param {Boolean/Number} appendArgs (optional) if True args are appended to call args instead of overriding,
     *                                             if a number the args are inserted at the specified position
     * @return {Function} The new function
     */
    createDelegate : function(obj, args, appendArgs){
        var method = this;
        return function() {
            var callArgs = args || arguments;
            if(appendArgs === true){
                callArgs = Array.prototype.slice.call(arguments, 0);
                callArgs = callArgs.concat(args);
            }else if(typeof appendArgs == "number"){
                callArgs = Array.prototype.slice.call(arguments, 0); // copy arguments first
                var applyArgs = [appendArgs, 0].concat(args); // create method call params
                Array.prototype.splice.apply(callArgs, applyArgs); // splice them in
            }
            return method.apply(obj || window, callArgs);
        };
    },

    /**
     * Calls this function after the number of millseconds specified.
     * @param {Number} millis The number of milliseconds for the setTimeout call (if 0 the function is executed immediately)
     * @param {Object} obj (optional) The object for which the scope is set
     * @param {Array} args (optional) Overrides arguments for the call. (Defaults to the arguments passed by the caller)
     * @param {Boolean/Number} appendArgs (optional) if True args are appended to call args instead of overriding,
     *                                             if a number the args are inserted at the specified position
     * @return {Number} The timeout id that can be used with clearTimeout
     */
    defer : function(millis, obj, args, appendArgs){
        var fn = this.createDelegate(obj, args, appendArgs);
        if(millis){
            return setTimeout(fn, millis);
        }
        fn();
        return 0;
    },
    /**
     * Create a combined function call sequence of the original function + the passed function.
     * The resulting function returns the results of the original function.
     * The passed fcn is called with the parameters of the original function
     * @param {Function} fcn The function to sequence
     * @param {Object} scope (optional) The scope of the passed fcn (Defaults to scope of original function or window)
     * @return {Function} The new function
     */
    createSequence : function(fcn, scope){
        if(typeof fcn != "function"){
            return this;
        }
        var method = this;
        return function() {
            var retval = method.apply(this || window, arguments);
            fcn.apply(scope || this || window, arguments);
            return retval;
        };
    },

    /**
     * Creates an interceptor function. The passed fcn is called before the original one. If it returns false, the original one is not called.
     * The resulting function returns the results of the original function.
     * The passed fcn is called with the parameters of the original function.
     * @addon
     * @param {Function} fcn The function to call before the original
     * @param {Object} scope (optional) The scope of the passed fcn (Defaults to scope of original function or window)
     * @return {Function} The new function
     */
    createInterceptor : function(fcn, scope){
        if(typeof fcn != "function"){
            return this;
        }
        var method = this;
        return function() {
            fcn.target = this;
            fcn.method = method;
            if(fcn.apply(scope || this || window, arguments) === false){
                return;
            }
            return method.apply(this || window, arguments);
        };
    }
});

Ext.applyIf(String, {

    /*
     * Escapes the passed string for ' and \
     * @param {String} str
     * @return {String}
     */
    escape : function(string) {
        return string.replace(/('|\\)/g, "\\$1");
    },

    leftPad : function (val, size, ch) {
        var result = new String(val);
        if (ch == null) {
            ch = " ";
        }
        while (result.length < size) {
            result = ch + result;
        }
        return result;
    },

    format : function(format){
        var args = Array.prototype.slice.call(arguments, 1);
        return format.replace(/\{(\d+)\}/g, function(m, i){
            return args[i];
        });
    }
});

String.prototype.toggle = function(value, other){
    return this == value ? other : value;
};

Ext.applyIf(Number.prototype, {
    constrain : function(min, max){
        return Math.min(Math.max(this, min), max);
    }
});

Ext.applyIf(Array.prototype, {
    indexOf : function(o){
       for (var i = 0, len = this.length; i < len; i++){
 	      if(this[i] == o) return i;
       }
 	   return -1;
    },

    remove : function(o){
       var index = this.indexOf(o);
       if(index != -1){
           this.splice(index, 1);
       }
    }
});

/**
 Returns the number of milliseconds between this date and date
 @param {Date} date (optional) Defaults to now
 @return {Number} The diff in milliseconds
 @member Date getElapsed
 */
Date.prototype.getElapsed = function(date) {
	return Math.abs((date || new Date()).getTime()-this.getTime());
};
