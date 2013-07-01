Kwf._contentJReadyHandlers = [];
Kwf._onJElementReadyHandlers = [];

/**
 * Register a function that will be called when content is loaded or shown
 * @param callback function
 * @param scope for callback
 * @param options supported are: priority (integer, higher number means it's called after all with lower number, default 0)
 */
Kwf.onJContentReady = function(fn, scope, options) {
    Kwf._contentJReadyHandlers.push({
        fn: fn,
        scope: scope,
        options: options || {}
    });
};

Kwf.callOnContentReadyJQuery = function(el, options) {
    Kwf._contentJReadyHandlers.sort(function(a, b) {
        return (a.options.priority || 0) - (b.options.priority || 0);
    });
    $.each(Kwf._contentJReadyHandlers, function(i, v) {
        v.fn.call(v.scope || window, (el || document.body), options);
    });
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
 * If an input type="hidden" is found directly under the element it's value gets passed
 * as config (json decoded)
 *
 * @param element selector
 * @param callback function
 * @param scope
 * @param options see onContentReady options, additionally checkVisibility (boolean, only call onElementReady when element is visible)
 */
Kwf.onJElementReady = function(selector, fn, scope, options) {
    Kwf._onJElementReadyHandlers.push({
        selector: selector,
        fn: fn,
        scope: scope,
        options: options || {},
        num: Kwf._onJElementReadyHandlers.length //unique number, used to mark in initDone
    });
};
Kwf.onJContentReady(function(addedEl, renderConfig) {
    Kwf._onJElementReadyHandlers.sort(function(a, b) {
        return (a.options.priority || 0) - (b.options.priority || 0);
    });
    for (var i=0; i<Kwf._onJElementReadyHandlers.length; i++) {
        var hndl = Kwf._onJElementReadyHandlers[i];
        $.each($(hndl.selector), function(i, el) {
            if (hndl.options.checkVisibility && !$(v).is(':visible')) return;
            if (!el.initDone) el.initDone = {};
            if (el.initDone[hndl.num]) return;
            el.initDone[hndl.num] = true;
            el = $(el);
            var config = {};
            var configEl = el.children('input[type="hidden"]');
            if (configEl) {
                try {
                    config = $.parseJSON(configEl.val());
                } catch (err) {}
            }
            hndl.fn.call(hndl.scope, el, config);
        });
    }
}, this);
