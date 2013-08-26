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
Kwf.onElementReady = function(selector, fn, scope, options) {
    Kwf._readyHandlers.push({
        selector: selector,
        fn: fn,
        scope: scope,
        options: options || {},
        num: Kwf._readyHandlers.length, //unique number, used to mark in initDone
        type: 'ext'
    });
};

Kwf._callOnElementReady = function(hndl, el) {
    Ext.query(hndl.selector, el).each(function(el) {
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
