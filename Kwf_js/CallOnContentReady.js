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
        //console.profile("callOnContentReady body");
        Kwf._resetOnReadyStats();
        Kwf.callOnContentReady(document.body, { action: 'render' });
        //console.profileEnd();
        //console.log(Kwf._onReadyStats);

        Ext.fly(window).on('resize', function() {
            Kwf.callOnContentReady(document.body, { action: 'widthChange' } );
        }, this, { buffer: 100 });
    });
}

Kwf._onReadyIsCalling = false;
Kwf._onReadyCallQueue = [];
Kwf._onReadyElQueue = [];
Kwf._elQueueNum = 0;
Kwf._onReadyElSortCache = {};
Kwf._elCacheBySelector = {};
Kwf._resetOnReadyStats = function() {
    Kwf._onReadyStats = {
        callOnContentReady: 0,
        onContentReady: 0,
        query: 0,
        queryTime: 0,
        querySkip: 0,
        queryCache: 0,
        onRender: 0,
        onShow: 0,
        onHide: 0,
        onWidthChange: 0,
        sort: 0,
        sortTime: 0,
        sortCacheMiss: 0,
        sortCacheHit: 0
    };
}

/**
 * @param element the added/changed dom element
 * @param options newRender (bool): if new elements have been added to the dom or just visiblity/width changed
 */
Kwf.callOnContentReady = function(renderedEl, options)
{
    Kwf._onReadyStats.callOnContentReady++;
    if (!options) options = {};
    if (options.newRender) {
        delete options.newRender;
        options.action = 'render';
    }
    if (!options.action) {
        if (console && console.warn) console.warn('Please set option action on callOnContentReady');
        options.action = 'render';
    }
    if (!renderedEl) {
        if (console && console && console.warn) console.warn('Please pass element argument on callOnContentReady');
        renderedEl = document.body;
    }
    if (Ext.Element && renderedEl instanceof Ext.Element) renderedEl = renderedEl.dom;
    if (jQuery && renderedEl instanceof jQuery) renderedEl = renderedEl.get();

    Kwf._onReadyCallQueue.push({
        renderedEl: renderedEl,
        options: options
    });

    var addToQueue = function(onActions) {
        for (var i = 0; i < Kwf._readyHandlers.length; i++) {
            var hndl = Kwf._readyHandlers[i];

            if (onActions.indexOf(hndl.onAction) != -1) {
                if (options.action != 'render' && Kwf._elCacheBySelector[hndl.selector]) {
                    if (Kwf._elCacheBySelector[hndl.selector].length === 0) {
                        //Optimize: if we never got element by that selector, skip query
                        Kwf._onReadyStats.querySkip++;
                        continue;
                    }
                    Kwf._onReadyStats.queryCache++;
                    var els = [];
                    for (var j=0; j<Kwf._elCacheBySelector[hndl.selector].length; j++) {
                        if (renderedEl == document.body || $.contains(renderedEl, Kwf._elCacheBySelector[hndl.selector][j])) {
                            els.push(Kwf._elCacheBySelector[hndl.selector][j]);
                        }
                    }
                } else {
                    Kwf._onReadyStats.query++;
                    var t = window.performance.now();
                    var els = $.makeArray($(renderedEl).find(hndl.selector));
                    t = window.performance.now() - t;
                    Kwf._onReadyStats.queryTime += t;
                    if (options.action == 'render' && renderedEl == document.body) {
                        Kwf._elCacheBySelector[hndl.selector] = els;
                    }
                }
                for (var j = 0; j< els.length; ++j) {
                    var alreadyInQueue = false;
                    Kwf._onReadyElQueue.each(function(q) {
                        if (q.num == hndl.num && q.el === els[j]) {
                            alreadyInQueue = true;
                            return true;
                        }
                    }, this);
                    if (!alreadyInQueue) {
                        var parentsCount = 0;
                        var n = els[j];
                        while (n = n.parentNode) {
                            parentsCount++;
                        }
                        Kwf._onReadyElQueue.push({
                            el: els[j],
                            fn: hndl.fn,
                            scope: hndl.scope,
                            options: hndl.options,
                            num: hndl.num,
                            type: hndl.type,
                            onAction: hndl.onAction,
                            selector: hndl.selector,
                            queueNum: Kwf._elQueueNum++,
                            priority: hndl.options.priority || 0,
                            parentsCount: parentsCount
                        });
                    }
                }
            }
        }
    };

    if (options.action == 'render') {
        addToQueue(['render', 'show', 'widthChange']);
    } else if (options.action == 'show') {
        addToQueue(['show', 'widthChange']);
    } else if (options.action == 'hide') {
        addToQueue(['hide']);
    } else if (options.action == 'widthChange') {
        addToQueue(['widthChange']);
    }

    Kwf._onReadyStats.sort++;
    var t = window.performance.now();
    Kwf._onReadyElQueue.sort(function sortOnReadyElQueue(a, b) {
        if (a.priority != b.priority) {
            return a.priority - b.priority;
        } else {
            if (a.parentsCount != b.parentsCount) {
                return a.parentsCount - b.parentsCount;
            } else {
                return 1;
            }
        }
    });
    t = window.performance.now() - t;
    Kwf._onReadyStats.sortTime += t;

    if (Kwf._onReadyIsCalling) {
        return;
    }

    Kwf._onReadyIsCalling = true;

    while (Kwf._onReadyCallQueue.length) {
        var queueEntry = Kwf._onReadyCallQueue.pop();
        renderedEl = queueEntry.renderedEl;
        options = queueEntry.options;

        Kwf._readyHandlers.sort(function(a, b) {
            return (a.options.priority || 0) - (b.options.priority || 0);
        });
        for (var i = 0; i < Kwf._readyHandlers.length; i++) {
            var hndl = Kwf._readyHandlers[i];
            if (hndl.selector == null) {
                Kwf._onReadyStats.onContentReady++;
                hndl.fn.call(hndl.scope || window, renderedEl, options);
            }
        }
    }

    while (Kwf._onReadyElQueue.length) {

        var queueEntry = Kwf._onReadyElQueue.shift();
        var el = queueEntry.el;
        if (queueEntry.onAction == 'render') {
            if (queueEntry.options.checkVisibility && Ext.fly(el).getWidth() == 0) {
                continue;
            }
            if (!el.initDone) el.initDone = {};
            if (el.initDone[queueEntry.num]) {
                continue;
            }
            el.initDone[queueEntry.num] = true;
            var config = {};
            var configEl = Ext.fly(el).child('input[type="hidden"]', true)
            if (configEl) {
                try {
                    config = $.parseJSON(configEl.value);
                } catch (err) {}
            }
            Kwf._onReadyStats.onRender++;

            if (queueEntry.type == 'ext') {
                queueEntry.fn.call(queueEntry.scope, Ext.get(el), config);
            } else if (queueEntry.type == 'jquery') {
                queueEntry.fn.call(queueEntry.scope, $(el), config);

            }
        } else {
            if (queueEntry.onAction == 'show') {
                if (Ext.fly(el).getWidth() > 0) {
                    Kwf._onReadyStats.onShow++;
                    queueEntry.fn.call(queueEntry.scope, Ext.get(el));
                }
            } else if (queueEntry.onAction == 'hide') {
                if (Ext.fly(el).getWidth() == 0) {
                    Kwf._onReadyStats.onHide++;
                    queueEntry.fn.call(queueEntry.scope, Ext.get(el));
                }
            } else if (queueEntry.onAction == 'widthChange') {
                Kwf._onReadyStats.onWidthChange++;
                queueEntry.fn.call(queueEntry.scope, Ext.get(el));
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
