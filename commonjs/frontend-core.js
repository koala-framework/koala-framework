if (typeof Kwf == 'undefined') Kwf = {};
Kwf.loadDeferred = function(fn) {
    if (!Kwf._loadDeferred) Kwf._loadDeferred = [];
    if (Kwf._loadDeferred === 'done') {
        fn();
    } else {
        Kwf._loadDeferred.push(fn);
    }
};
