/* fn: fn, 
 * scope: scope, 
 * options: options || {}, 
 * type: [jquery|ext],
 * num: unique number, //used to mark in initDone
 * selector: selector, // null if onContentReady
 * */
Kwf._readyHandlers = [];

if (!Kwf.isApp) {
    $(document).ready(function() {
        if (!document.body) {
            //this happens if a redirect by changing location in JS is done
            //in that case no contentReady needs to be called
            return;
        }
        Kwf.callOnContentReady(document.body, { newRender: true });
    });
}

Kwf.callOnContentReady = function(el, options) {
    if (!options) options = {};
    if (Ext.Element && el instanceof Ext.Element) el = el.dom;

    Kwf._readyHandlers.sort(function(a, b) {
        return (a.options.priority || 0) - (b.options.priority || 0);
    });
    for (var i = 0; i < Kwf._readyHandlers.length; i++) {
        var hndl = Kwf._readyHandlers[i];
        if (hndl.selector == null) {
            hndl.fn.call(hndl.scope || window, (el || document.body), options);
        } else {
            if (hndl.type == 'jquery') { // OnElementReady jQuery
                Kwf._callOnJElementReady(hndl, el);

            } else if (hndl.type == 'ext') { // OnElementReady ExtJS
                Kwf._callOnElementReady(hndl, el);
            }
        }
    }
};

/**
 * Register a function that will be called when content is loaded or shown
 * @param callback function
 * @param scope for callback
 * @param options supported are: priority (integer, higher number means it's called after all with lower number, default 0)
 */
Kwf.onContentReady = function(fn, scope, options) {
    Kwf._readyHandlers.push({
        selector: null,
        fn: fn,
        scope: scope,
        options: options || {}
    });
};
