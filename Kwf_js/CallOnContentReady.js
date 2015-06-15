(function() {

var enableOnReadyConsoleProfile = false;

/* fn: fn,
 * scope: scope,
 * options: options || {},
 * num: unique number, //used to mark in initDone
 * selector: selector, // null if onContentReady
 * */
var readyElHandlers = []; //for onElement*
var onReadyState;  //if callOnContentReady should skip 'defer' callbacks or execute only 'defer' callbacks
                   //used on initial ready
var deferredStart = null; //used for timing deferred, required here because of multiple chunks
var onReadyIsCalling = false; //true while callOnContentReady is processing onReadyElQueue and we can add more to queue
var onReadyElQueue = []; //queue for onElementReady/Show/Hide/WidthChange calls
var elCacheBySelector = {};

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
    Kwf._addReadyHandler('contentReady', null, fn, options);
};


var deferHandlerNum = null;
Kwf._addReadyHandler = function(onAction, selector, fn, options)
{
    if (!options) options = {};
    if (typeof options.defer == 'undefined') options.defer = true; //default defer=true
    readyElHandlers.push({
        selector: selector,
        fn: fn,
        options: options,
        num: readyElHandlers.length, //unique number
        onAction: onAction
    });
    //if initial call is already done redo for new added handlers
    if (onReadyState == 'calledDefer' || (!(options && options.defer) && onReadyState == 'calledNonDefer')) {
        if (!deferHandlerNum) {
            deferHandlerNum = [];
            setTimeout(function() {
                //additionally added handlers need to be called async so priorty can be used correctly (we wait to get more handlers added)
                //needed for Frontend.defer.js
                var hndlerNum = deferHandlerNum;
                deferHandlerNum = null; //set to null before calling callOnContentReady as that might cause _addReadyHandler calls
                Kwf.callOnContentReady(document.body, { action: 'render', handlerNum: hndlerNum });
            }, 1);
        }
        deferHandlerNum.push(readyElHandlers.length-1);
    }
};

/**
 * @param element the added/changed dom element
 * @param options newRender (bool): if new elements have been added to the dom or just visiblity/width changed
 */
Kwf.callOnContentReady = function(renderedEl, options)
{
    Kwf.Utils.BenchmarkBox.count('callOnContentReady');
    if (!options) options = {};
    if (typeof options.newRender != 'undefined') {
        //backwards compatibility, for callOnContentReady call
        options.action = options.newRender ? 'render' : 'show';
        delete options.newRender;
    }
    if (!options.action) {
        if (typeof console != 'undefined' && console.warn) console.warn('Please set option action on callOnContentReady');
        options.action = 'render';
    }
    if (!renderedEl) {
        if (typeof console != 'undefined' && console.warn) console.warn('Please pass element argument on callOnContentReady');
        renderedEl = document.body;
    }
    if (renderedEl.dom) renderedEl = renderedEl.dom; //ExtJS Element (hopefully)
    if ($ && renderedEl instanceof $) {
        renderedEl.each(function(){ Kwf.callOnContentReady(this, options); });
        return;
    }

    //add entries to onReadyElQueue, depending on renderedEl
    var onActions;
    if (options.action == 'render') {
        onActions = ['render', 'show', 'widthChange', 'contentReady'];
    } else if (options.action == 'show') {
        onActions = ['show', 'widthChange', 'contentReady'];
    } else if (options.action == 'hide') {
        onActions = ['hide'];
    } else if (options.action == 'widthChange') {
        onActions = ['widthChange', 'contentReady'];
    }

    var html = false;
    for (var i = 0; i < readyElHandlers.length; i++) {
        var hndl = readyElHandlers[i];
        if (options.handlerNum && $.inArray(hndl.num, options.handlerNum) == -1) {
            continue;
        }

        //onReadyState gets set before callOnContentReady(body)
        //can not be part of options as it would be missing in recursive calls
        if (onReadyState == 'callNonDefer') {
            if (hndl.options.defer) {
                continue;
            }
        } else if (onReadyState == 'callDefer') {
            if (!hndl.options.defer) {
                continue;
            }
        }

        if (hndl.options.checkVisibility) {
            if (renderedEl != document.body && !Kwf.Utils.Element.isVisible(renderedEl)) {
                if (options.action == 'render' && hndl.selector && elCacheBySelector[hndl.selector]) {
                    //mark cache as dirty as we don't query (and update the selector) as the renderedEl is invisible
                    //TODO don't always mark as dirty especially when we have multiple handlers with same selector
                    elCacheBySelector[hndl.selector].dirty = true;
                }
                continue;
            }
            //if checkVisibility is activated, don't skip based on onActions as
            //even a widthChange action could make an element visible (media queries)
        } else {
            if ($.inArray(hndl.onAction, onActions) == -1) {
                continue;
            }
        }

        if (options.action == 'render' && !html) {
            var t = Kwf.Utils.BenchmarkBox.now();
            html = renderedEl.innerHTML;
            Kwf.Utils.BenchmarkBox.time('innerHTML', Kwf.Utils.BenchmarkBox.now()-t);
        }

        var useSelectorCache;
        if (options.action != 'render' || !hndl.selector) {
            useSelectorCache = true;
        } else {
            if (onReadyState == 'callDefer' && renderedEl == document.body && elCacheBySelector[hndl.selector]) {
                useSelectorCache = true;
            } else {
                var t = Kwf.Utils.BenchmarkBox.now();
                var m = hndl.selector.match(/^[a-z]*\.([a-z]+)/i);
                //do a stupid text search on the selector, using that we can skip query for many selectors that don't exist in the current el
                if (m && html.indexOf(m[1]) == -1) {
                    useSelectorCache = true;
                    if (!elCacheBySelector[hndl.selector]) {
                        elCacheBySelector[hndl.selector] = [];
                    }
                } else {
                    useSelectorCache = false;
                }
                Kwf.Utils.BenchmarkBox.time('checkInnerHtml', Kwf.Utils.BenchmarkBox.now()-t);
            }
        }

        if (useSelectorCache && elCacheBySelector[hndl.selector] && !elCacheBySelector[hndl.selector].dirty) {
            Kwf.Utils.BenchmarkBox.count('queryCache');
            var els = [];
            for (var j=0; j<elCacheBySelector[hndl.selector].length; j++) {
                if (renderedEl == document.body ||
                    renderedEl == elCacheBySelector[hndl.selector][j] ||
                    $.contains(renderedEl, elCacheBySelector[hndl.selector][j])
                ) {
                    els.push(elCacheBySelector[hndl.selector][j]);
                }
            }
        } else if (!hndl.selector) {
            var els = $.makeArray($(renderedEl));
        } else {
            var t = Kwf.Utils.BenchmarkBox.now();
            var els = $.makeArray($(renderedEl).find(hndl.selector));
            if (window.matchesSelector(renderedEl, hndl.selector)) {
                els.push(renderedEl);
            }
            Kwf.Utils.BenchmarkBox.time('query', Kwf.Utils.BenchmarkBox.now() - t);
            if (!elCacheBySelector[hndl.selector]) {
                elCacheBySelector[hndl.selector] = els;
            } else {
                for(var j=0; j<els.length; ++j) {
                    if ($.inArray(els[j], elCacheBySelector[hndl.selector]) == -1) {
                        elCacheBySelector[hndl.selector].push(els[j]);
                    }
                }
            }
        }

        for (var j = 0; j< els.length; ++j) {

            var alreadyInQueue = false;
            $.each(onReadyElQueue, function(indx, queueEntry) {
                if (queueEntry.num == hndl.num && els[j] == queueEntry.el) {
                    alreadyInQueue = true;
                }
            });

            if (!alreadyInQueue) {
                var parentsCount = 0;
                var n = els[j];
                while (n = n.parentNode) {
                    parentsCount++;
                }
                Kwf.Utils.BenchmarkBox.count('readyEl');
                onReadyElQueue.push({
                    el: els[j],
                    fn: hndl.fn,
                    options: hndl.options,
                    num: hndl.num,
                    callerOptions: options,
                    onAction: hndl.onAction,
                    selector: hndl.selector,
                    priority: hndl.options.priority || 0,
                    parentsCount: parentsCount
                });
            }
        }
    }

    //sort onReadyElQueue
    var t = Kwf.Utils.BenchmarkBox.now();
    onReadyElQueue.sort(function sortOnReadyElQueue(a, b) {
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

    if (onReadyIsCalling) {
        return;
    }

    onReadyIsCalling = true;

    //call the callback of an element ready handler
    function callQueueFn(queueEntry, config)
    {
        var t = Kwf.Utils.BenchmarkBox.now();
        var el = queueEntry.el;
        if (queueEntry.onAction != 'contentReady') {
            el = $(el);
        }
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

    //process one entry of the readyElQueue
    //own function so it can be called in sync or in deferred chunks
    function processOnReadyElQueueEntry()
    {
        var queueEntry = onReadyElQueue.shift();
        var el = queueEntry.el;
        if (queueEntry.onAction == 'render') {
            if (queueEntry.options.checkVisibility && !Kwf.Utils.Element.isVisible(el)) {
                return;
            }
            if (!el.initDone) el.initDone = {};
            if (el.initDone[queueEntry.num]) {
                return;
            }
            el.initDone[queueEntry.num] = true;
            var config = {};
            var configEl = $(el).find('> input[type="hidden"]');
            if (configEl.length) {
                try {
                    var v = configEl.get(0).value;
                    if (v.substr(0, 1) == '{' || v.substr(0, 1) == '[') {
                        config = $.parseJSON(v);
                    }
                } catch (err) {}
            }
            callQueueFn(queueEntry, config);
        } else if (queueEntry.onAction == 'show') {
            if (Kwf.Utils.Element.isVisible(el)) {
                callQueueFn(queueEntry);
            }
        } else if (queueEntry.onAction == 'hide') {
            if (!Kwf.Utils.Element.isVisible(el)) {
                callQueueFn(queueEntry);
            }
        } else if (queueEntry.onAction == 'widthChange') {
            if (!queueEntry.options.checkVisibility || Kwf.Utils.Element.isVisible(el)) {
                callQueueFn(queueEntry);
            }
        } else if (queueEntry.onAction == 'contentReady') {
            var options = {
                newRender: (queueEntry.callerOptions.action == 'render'),
                action: queueEntry.callerOptions.action
            };
            callQueueFn(queueEntry, options);
        }
    }

    if (onReadyState == 'callDefer') {
        var processNext = function processNext() {
            Kwf.Utils.BenchmarkBox.count('chunks');
            var t = Kwf.Utils.BenchmarkBox.now();
            while (onReadyElQueue.length && Kwf.Utils.BenchmarkBox.now()-t < 50) {
                processOnReadyElQueueEntry();
            }
            if (onReadyElQueue.length) {
                setTimeout(processNext, 1);
            } else {
                onReadyIsCalling = false;
                Kwf.Utils.BenchmarkBox.time('time', Kwf.Utils.BenchmarkBox.now()-deferredStart);
                if (enableOnReadyConsoleProfile) console.profileEnd();
                Kwf.Utils.BenchmarkBox.create({
                    counters: Kwf._onReadyStats,
                    type: 'onReady defer'
                });
            }
        };
        processNext();
    } else {
        while (onReadyElQueue.length) {
            processOnReadyElQueueEntry();
        }
        onReadyIsCalling = false;
    }

};


if (!Kwf.isApp) {
    $(document).ready(function() {
        if (!document.body) {
            //this happens if a redirect by changing location in JS is done
            //in that case no contentReady needs to be called
            return;
        }
        var t = Kwf.Utils.BenchmarkBox.now();
        if (enableOnReadyConsoleProfile) console.profile("callOnContentReady body");
        onReadyState = 'callNonDefer';
        Kwf.callOnContentReady(document.body, { action: 'render' });
        onReadyState = 'calledNonDefer';
        if (enableOnReadyConsoleProfile) console.profileEnd();
        Kwf.Utils.BenchmarkBox.time('time', Kwf.Utils.BenchmarkBox.now()-t);
        Kwf.Utils.BenchmarkBox.create({
            counters: Kwf._onReadyStats,
            type: 'onReady'
        });
        setTimeout(function() {
            deferredStart = Kwf.Utils.BenchmarkBox.now();
            if (enableOnReadyConsoleProfile) console.profile("callOnContentReady body deferred");
            onReadyState = 'callDefer';
            Kwf.callOnContentReady(document.body, { action: 'render' });
            onReadyState = 'calledDefer';
        }, 10);

        var timeoutId;
        $(window).resize(function() {
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
            timeoutId = setTimeout(function() {
                Kwf.callOnContentReady(document.body, { action: 'widthChange' } );
            }, 100);
        });
    });
}

})();
