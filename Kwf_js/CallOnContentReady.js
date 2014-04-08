Kwf.enableOnReadyConsoleProfile = false;

/* fn: fn,
 * scope: scope,
 * options: options || {},
 * type: [jquery|ext],
 * num: unique number, //used to mark in initDone
 * selector: selector, // null if onContentReady
 * */
Kwf._readyHandlers = [];
Kwf._skipDeferred; //if callOnContentReady should skip 'defer' callbacks or execute only 'defer' callbacks
                   //used on initial ready
Kwf._deferredStart = null; //used for timing deferred, required here because of multiple chunks
Kwf._onReadyIsCalling = false; //true while callOnContentReady is processing onReadyElQueue and we can add more to queue
Kwf._onReadyCallQueue = []; //queue for onContentReady to avoid recursions
Kwf._onReadyElQueue = []; //queue for onElementReady/Show/Hide/WidthChange calls
Kwf._elCacheBySelector = {};



if (!Kwf.isApp) {
    $(document).ready(function() {
        if (!document.body) {
            //this happens if a redirect by changing location in JS is done
            //in that case no contentReady needs to be called
            return;
        }
        var t = Kwf.Utils.BenchmarkBox.now();
        if (Kwf.enableOnReadyConsoleProfile) console.profile("callOnContentReady body");
        Kwf._skipDeferred = true;
        Kwf.callOnContentReady(document.body, { action: 'render' });
        delete Kwf._skipDeferred;
        if (Kwf.enableOnReadyConsoleProfile) console.profileEnd();
        Kwf.Utils.BenchmarkBox.time('time', Kwf.Utils.BenchmarkBox.now()-t);
        Kwf.Utils.BenchmarkBox.create({
            counters: Kwf._onReadyStats,
            type: 'onReady'
        });
        (function() {
            Kwf._deferredStart = Kwf.Utils.BenchmarkBox.now();
            if (Kwf.enableOnReadyConsoleProfile) console.profile("callOnContentReady body deferred");
            Kwf._skipDeferred = false;
            Kwf.callOnContentReady(document.body, { action: 'render' });
            delete Kwf._skipDeferred;
        }).defer(10);

        Ext.fly(window).on('resize', function() {
            Kwf.callOnContentReady(document.body, { action: 'widthChange' } );
        }, this, { buffer: 100 });
    });
}

Kwf._addReadyHandler = function(type, onAction, selector, fn, options)
{
    Kwf._readyHandlers.push({
        selector: selector,
        fn: fn,
        options: options || {},
        num: Kwf._readyHandlers.length, //unique number
        type: type,
        onAction: onAction
    });
};

/**
 * @param element the added/changed dom element
 * @param options newRender (bool): if new elements have been added to the dom or just visiblity/width changed
 */
Kwf.callOnContentReady = function(renderedEl, options)
{
    Kwf.Utils.BenchmarkBox.count('callOnContentReady');
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

    if (options.action == 'render') {
        //new elements rendered, clear cache
        Kwf._elCacheBySelector = {};

        var t = Kwf.Utils.BenchmarkBox.now();
        var html = renderedEl.innerHTML;
        Kwf.Utils.BenchmarkBox.time('innerHTML', Kwf.Utils.BenchmarkBox.now()-t);
    }


    var addToQueue = function(onActions) {


        for (var i = 0; i < Kwf._readyHandlers.length; i++) {
            var hndl = Kwf._readyHandlers[i];


            //Kwf._skipDeferred gets set before callOnContentReady(body)
            //can not be part of options as it would be missing in recursive calls
            if (Kwf._skipDeferred === true) {
                if (hndl.options.defer) {
                    continue;
                }
            } else if (Kwf._skipDeferred === false) {
                if (!hndl.options.defer) {
                    continue;
                }
            }

            if (hndl.options.checkVisibility) {
                if (renderedEl != document.body && !$(renderedEl).is(':visible')) {
                    continue;
                }
                //if checkVisibility is activated, don't skip based on onActions as
                //even a widthChange action could make an element visible (media queries)
            } else {
                if (onActions.indexOf(hndl.onAction) == -1) {
                    continue;
                }
            }

            if (options.action == 'render' && !Kwf._elCacheBySelector[hndl.selector]) {
                var t = Kwf.Utils.BenchmarkBox.now();
                var m = hndl.selector.match(/^[a-z]*\.([a-z]+)/i)
                if (m) {
                    //do a stupid text search on the selector, using that we can skip query for many selectors that don't exist in the current el
                    if (html.indexOf(m[1]) == -1) {
                        Kwf._elCacheBySelector[hndl.selector] = [];
                    }
                }
                Kwf.Utils.BenchmarkBox.time('checkInnerHtml', Kwf.Utils.BenchmarkBox.now()-t);
            }

            if (Kwf._elCacheBySelector[hndl.selector]) {
                if (Kwf._elCacheBySelector[hndl.selector].length === 0) {
                    //Optimize: if we never got element by that selector, skip query
                    Kwf.Utils.BenchmarkBox.count('querySkip');
                    continue;
                }
                Kwf.Utils.BenchmarkBox.count('queryCache');
                var els = [];
                for (var j=0; j<Kwf._elCacheBySelector[hndl.selector].length; j++) {
                    if (renderedEl == document.body || $.contains(renderedEl, Kwf._elCacheBySelector[hndl.selector][j])) {
                        els.push(Kwf._elCacheBySelector[hndl.selector][j]);
                    }
                }
            } else {
                var t = Kwf.Utils.BenchmarkBox.now();
                var els = $.makeArray($(renderedEl).find(hndl.selector));
                Kwf.Utils.BenchmarkBox.time('query', Kwf.Utils.BenchmarkBox.now() - t);
                if (options.action == 'render' && renderedEl == document.body) {
                    Kwf._elCacheBySelector[hndl.selector] = els;
                }
            }
            for (var j = 0; j< els.length; ++j) {

                if (!els[j].onReadyQueue) els[j].onReadyQueue = [];
                var alreadyInQueue = els[j].onReadyQueue.indexOf(hndl.num) != -1;

                if (!alreadyInQueue) {
                    els[j].onReadyQueue.push(hndl.num);
                    var parentsCount = 0;
                    var n = els[j];
                    while (n = n.parentNode) {
                        parentsCount++;
                    }
                    Kwf.Utils.BenchmarkBox.count('readyEl');
                    Kwf._onReadyElQueue.push({
                        el: els[j],
                        fn: hndl.fn,
                        options: hndl.options,
                        num: hndl.num,
                        type: hndl.type,
                        onAction: hndl.onAction,
                        selector: hndl.selector,
                        priority: hndl.options.priority || 0,
                        parentsCount: parentsCount
                    });
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

    var t = Kwf.Utils.BenchmarkBox.now();
    Kwf._onReadyElQueue.sort(function sortOnReadyElQueue(a, b) {
        if (a.priority != b.priority) {
            return a.priority - b.priority;
        } else {
            if (a.parentsCount != b.parentsCount) {
                return a.parentsCount - b.parentsCount;
            } else {
                return a.num - b.num;
            }
        }
    });
    Kwf.Utils.BenchmarkBox.time('sort', Kwf.Utils.BenchmarkBox.now() - t);

    if (Kwf._onReadyIsCalling) {
        return;
    }

    Kwf._onReadyIsCalling = true;

    while (Kwf._onReadyCallQueue.length) {
        var queueEntry = Kwf._onReadyCallQueue.pop();

        Kwf._readyHandlers.sort(function(a, b) {
            return (a.options.priority || 0) - (b.options.priority || 0);
        });
        for (var i = 0; i < Kwf._readyHandlers.length; i++) {
            var hndl = Kwf._readyHandlers[i];
            if (hndl.selector == null) {
                var t = Kwf.Utils.BenchmarkBox.now();
                hndl.fn.call(hndl.options.scope || window, queueEntry.renderedEl, queueEntry.options);
                var fnName = hndl.fn.name;
                if (!fnName) {
                    fnName = 'unknown';
                }
                Kwf.Utils.BenchmarkBox.subTime('onContentReady', 'fn: '+fnName, Kwf.Utils.BenchmarkBox.now()-t);

            }
        }
    }

    function _callQueueFn(queueEntry, config)
    {
        var t = Kwf.Utils.BenchmarkBox.now();
        var el = queueEntry.el;
        el = queueEntry.type == 'ext' ? Ext.get(el) : $(el);
        queueEntry.fn.call(queueEntry.options.scope || window, el, config);
        var fnName = queueEntry.fn.name;
        if (!fnName) {
            fnName = 'unknown';
        }

        Kwf.Utils.BenchmarkBox.subTime(
            'on'+queueEntry.onAction.charAt(0).toUpperCase() + queueEntry.onAction.slice(1),
            'fn: '+fnName,
            Kwf.Utils.BenchmarkBox.now()-t
        );
    };
    function _processOnReadyElQueueEntry()
    {
        var queueEntry = Kwf._onReadyElQueue.shift();
        var el = queueEntry.el;
        el.onReadyQueue.splice(el.onReadyQueue.indexOf(queueEntry.num), 1);
        if (queueEntry.onAction == 'render') {
            if (queueEntry.options.checkVisibility && !$(el).is(':visible')) {
                return;
            }
            if (!el.initDone) el.initDone = {};
            if (el.initDone[queueEntry.num]) {
                return;
            }
            el.initDone[queueEntry.num] = true;
            var config = {};
            var configEl = $(el).find('> input[type="hidden"]')
            if (configEl.length) {
                try {
                    var v = configEl.get(0).value;
                    if (v.substr(0, 1) == '{') {
                        config = $.parseJSON(v);
                    }
                } catch (err) {}
            }
            _callQueueFn(queueEntry, config);
        } else {
            if (queueEntry.onAction == 'show') {
                if ($(el).is(':visible')) {
                    _callQueueFn(queueEntry);
                }
            } else if (queueEntry.onAction == 'hide') {
                if (!$(el).is(':visible')) {
                    _callQueueFn(queueEntry);
                }
            } else if (queueEntry.onAction == 'widthChange') {
                _callQueueFn(queueEntry);
            }

        }
    }

    if (Kwf._skipDeferred === false) {
        var processNext = function processNext() {
            Kwf.Utils.BenchmarkBox.count('chunks');
            var t = Kwf.Utils.BenchmarkBox.now();
            while (Kwf._onReadyElQueue.length && Kwf.Utils.BenchmarkBox.now()-t < 50) {
                _processOnReadyElQueueEntry();
            }
            if (Kwf._onReadyElQueue.length) {
                processNext.defer(this, 1);
            } else {
                Kwf._onReadyIsCalling = false;
                Kwf.Utils.BenchmarkBox.time('time', Kwf.Utils.BenchmarkBox.now()-Kwf._deferredStart);
                if (Kwf.enableOnReadyConsoleProfile) console.profileEnd();
                Kwf.Utils.BenchmarkBox.create({
                    counters: Kwf._onReadyStats,
                    type: 'onReady defer'
                });
            }
        };
        processNext();
    } else {
        while (Kwf._onReadyElQueue.length) {
            _processOnReadyElQueueEntry();
        }
        Kwf._onReadyIsCalling = false;
    }

};


/**
 * Register a function that will be called when content is loaded or shown
 * @param callback function (passed arguments: el, options (newRender=bool))
 * @param options supported are: priority (integer, higher number means it's called after all with lower number, default 0)
 */
Kwf.onContentReady = function(fn, options) {
    if (arguments.length == 3) {
        var scope = arguments[1];
        var options = arguments[2];
        options.scope = scope;
    }
    Kwf._readyHandlers.push({
        selector: null,
        fn: fn,
        options: options || {}
    });
};
