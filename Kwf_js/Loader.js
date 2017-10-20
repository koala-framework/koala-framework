(function() {

var _callbacks = {};
var _loaded = [];
var config = window.Kwf.Loader.config;

window.Kwf.Loader.require = function(expression, fn, scope)
{
    if (!scope) scope = window;

    var classIsLoaded = false;
    var Ext = window.Ext4;
    if (Ext && Ext.ClassManager && Ext.ClassManager.isCreated(expression)) {
        classIsLoaded = true;
    }
    if (classIsLoaded) {
        fn.call(scope);
        return;
    }
    if (_callbacks[expression]) {
        _callbacks[expression].push({fn: fn, scope: scope});
        return;
    }
    var src = KWF_BASE_URL+'/assets/dependencies/Kwf_Assets_Package_LazyLoad/'+config.providerList+':'+expression+':'+_loaded.join(',')+'/en/0/js';
    _callbacks[expression] = [];
    _callbacks[expression].push({fn: fn, scope: scope});

    var head = document.head || document.getElementsByTagName("head")[0];

    var scriptEl = document.createElement('script');
    scriptEl.src = src;
    scriptEl.async = true;
    scriptEl.onload = function() {
        for(var i=0; i<_callbacks[expression].length; i++) {
            var cb = _callbacks[expression][i];
            cb.fn.call(cb.scope);
        }
        _callbacks[expression] = [];
    };
    head.appendChild(scriptEl);

    src = KWF_BASE_URL+'/assets/dependencies/Kwf_Assets_Package_LazyLoad/'+config.providerList+':'+expression+':'+_loaded.join(',')+'/en/0/css';
    var styleEl = document.createElement('link');
    styleEl.type = 'text/css';
    styleEl.rel = 'stylesheet';
    styleEl.href = src;
    head.appendChild(styleEl);

    _loaded.push(expression);
};
})();
