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


Kwf._onReadyIsCalling = false;
Kwf._onReadyCallQueue = [];
/**
 * @param element the added/changed dom element
 * @param options newRender (bool): if new elements have been added to the dom or just visiblity/width changed
 */
Kwf.callOnContentReady = function(el, options)
{
    Kwf._onReadyCallQueue.push({
        el: el,
        options: options
    });

    if (Kwf._onReadyIsCalling) {
        return;
    }

    while (Kwf._onReadyCallQueue.length) {
        Kwf._onReadyIsCalling = true;
        var queueEntry = Kwf._onReadyCallQueue.pop();
        el = queueEntry.el;
        options = queueEntry.options;

        if (!options) options = {};
        if (Ext.Element && el instanceof Ext.Element) el = el.dom;
        if (jQuery && el instanceof jQuery) el = el.get();
        if (typeof options.newRender === 'undefined') {
            if (console && console.warn) console.warn('Please set option newRender: true|false on callOnContentReady');
            options.newRender = true;
        }
        if (!el) {
            if (console && console && console.warn) console.warn('Please pass element argument on callOnContentReady');
        }

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
    }
    Kwf._onReadyIsCalling = false;
};

/**
 * Register a function that will be called when content is loaded or shown
 * @param callback function (passed arguments: el, options (newRender=bool))
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
