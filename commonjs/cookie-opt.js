var $ = require('jquery');
var defaultAdapter = require('kwf/commonjs/cookie-opt/adapter');

var promise = $.Deferred();
var adapterPromise;
var adapter;

setTimeout(function() {
    if (!adapter) adapter = defaultAdapter;
}, 1);

module.exports = {
    setAdapter: function(a) {
        adapter = a;
    },
    load: function(callback) {
        if (!adapterPromise) {
            adapterPromise = $.Deferred();
            if (!adapter) adapter = defaultAdapter;
            adapter(adapterPromise);
            adapterPromise.done(function(api) {
                promise.resolve(api);
            });
        }
        promise.done(function(api) {
            callback(api);
        });
    }
}
