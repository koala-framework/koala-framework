/**
 * Register a function that will be called when content is loaded or shown
 * @param callback function
 * @param scope for callback
 * @param options supported are: priority (integer, higher number means it's called after all with lower number, default 0)
 */
Kwf.onJContentReady = function(fn, scope, options) {
    Kwf._readyHandlers.push({
        selector: null,
        fn: fn,
        scope: scope,
        options: options || {},
        type: 'jquery'
    });
};

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
    Kwf._readyHandlers.push({
        selector: selector,
        fn: fn,
        scope: scope,
        options: options || {},
        num: Kwf._readyHandlers.length, //unique number, used to mark in initDone
        type: 'jquery'
    });
};

Kwf._callOnJElementReady = function(hndl, el) {
    $.each($(hndl.selector), function(i, el) {
        if (hndl.options.checkVisibility && !$(v).is(':visible')) return;
        if (!el.initDone) el.initDone = {};
        if (el.initDone[hndl.num]) return;
        el.initDone[hndl.num] = true;
        el = $(el);
        var config = {};
        var configEl = el.children('input[type="hidden"]');
        if (configEl && configEl.length > 0) {
            try {
                config = $.parseJSON(configEl.val());
            } catch (err) {}
        }
        hndl.fn.call(hndl.scope, el, config);
    });
}
