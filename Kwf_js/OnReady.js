Kwf._contentReadyHandlers = [];
Kwf._onElementReadyHandlers = [];

/**
 * Register a function that will be called when content is loaded or shown
 * @param callback function
 * @param scope for callback
 * @param options supported are: priority (integer, higher number means it's called after all with lower number, default 0)
 */
Kwf.onContentReady = function(fn, scope, options) {
    Kwf._contentReadyHandlers.push({
        fn: fn,
        scope: scope,
        options: options || {}
    });
};

Kwf.callOnContentReady = function(el, options) {
    if (!options) options = {};
    Kwf._contentReadyHandlers.sort(function(a, b) {
        return (a.options.priority || 0) - (b.options.priority || 0);
    });
    if (el instanceof Ext.Element) el = el.dom;
    Ext.each(Kwf._contentReadyHandlers, function(i) {
        i.fn.call(i.scope || window, (el || document.body), options);
    }, this);
};

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

/**
 * Add a callback function that gets called once for every element that appears
 * in the dom tree
 *
 * If ainput type="hidden" is found directly under the element it's value gets passed
 * as config (json decoded)
 *
 * @param element selector
 * @param callback function
 * @param scope
 * @param options see onContentReady options, additionally checkVisibility (boolean, only call onElementReady when element is visible)
 */
Kwf.onElementReady = function(selector, fn, scope, options) {
    Kwf._onElementReadyHandlers.push({
        selector: selector,
        fn: fn,
        scope: scope,
        options: options || {},
        num: Kwf._onElementReadyHandlers.length //unique number, used to mark in initDone
    });
};

Kwf.onContentReady(function(addedEl, renderConfig) {
    Kwf._onElementReadyHandlers.sort(function(a, b) {
        return (a.options.priority || 0) - (b.options.priority || 0);
    });
    for (var i=0; i<Kwf._onElementReadyHandlers.length; i++) {
        var hndl = Kwf._onElementReadyHandlers[i];
        Ext.query(hndl.selector, addedEl).each(function(el) {
            if (hndl.options.checkVisibility && !Ext.fly(el).isVisible(true)) return;

            if (!el.initDone) el.initDone = {};
            if (el.initDone[hndl.num]) return;
            el.initDone[hndl.num] = true;

            el = Ext.get(el);
            var config = {};
            var configEl = el.child('> input[type="hidden"]')
            if (configEl) {
                try {
                    config = Ext.decode(configEl.getValue());
                } catch (err) {}
            }
            hndl.fn.call(hndl.scope, el, config);
        }, this);
    }
}, this);
