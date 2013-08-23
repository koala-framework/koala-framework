/* fn: fn, 
 * scope: scope, 
 * options: options || {}, 
 * type: [jquery|ext],
 * num: unique number, //used to mark in initDone
 * selector: selector, // null if onContentReady
 * */
Kwf._readyHandlers = [];

Kwf.callOnContentReady = function(el, options) {
    if (!options) options = {};
    if (Ext.Element && el instanceof Ext.Element) el = el.dom;

    Kwf._readyHandlers.sort(function(a, b) {
        return (a.options.priority || 0) - (b.options.priority || 0);
    });
    for (var i = 0; i < Kwf._readyHandlers.length; i++) {
        var hndl = Kwf._readyHandlers[i];
        if (hndl.selector == null) { // this is onJContentReady
            hndl.fn.call(hndl.scope || window, (el || document.body), options);
        } else {
            if (hndl.type == 'jquery') { // OnElementReady jQuery
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

            } else if (hndl.type == 'ext') { // OnElementReady ExtJS
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
        }
    }
};
