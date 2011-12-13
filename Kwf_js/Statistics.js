Ext.ns('Kwf.Statistics');

Kwf.Statistics.counter = [];

Kwf.Statistics.onCount = function(fn) {
    Kwf.Statistics.counter.push(fn);
};

Kwf.Statistics.count = function(url, config) {
    if (!config) config = {};
    Kwf.Statistics.counter.forEach(function(c) {
        c.call(this, url, config);
    }, this);
};
