var _ = require('underscore');

if (typeof Array.prototype.forEach != 'function') {
    Array.prototype.forEach = function (fn, scope) {
        _.forEach(this, fn, scope);
    };
}

//source: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Function/bind
if (!Function.prototype.bind) {
    Function.prototype.bind = function (oThis) {
        if (typeof this !== 'function') {
            // closest thing possible to the ECMAScript 5
            // internal IsCallable function
            throw new TypeError('Function.prototype.bind - what is trying to be bound is not callable');
        }

        var aArgs = Array.prototype.slice.call(arguments, 1),
            fToBind = this,
            fNOP = function () {
            },
            fBound = function () {
                return fToBind.apply(this instanceof fNOP
                        ? this
                        : oThis,
                    aArgs.concat(Array.prototype.slice.call(arguments)));
            };

        if (this.prototype) {
            // native functions don't have a prototype
            fNOP.prototype = this.prototype;
        }
        fBound.prototype = new fNOP();

        return fBound;
    };
}

if (typeof Kwf == 'undefined') Kwf = {};
Kwf.loadDeferred = function(fn) {
    if (!Kwf._loadDeferred) Kwf._loadDeferred = [];
    if (Kwf._loadDeferred === 'done') {
        fn();
    } else {
        Kwf._loadDeferred.push(fn);
    }
};
