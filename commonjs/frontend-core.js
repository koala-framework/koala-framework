var _ = require('underscore');

_.extend(Array.prototype, {
    //deprecated! -> forEach (ist auch ein JS-Standard!)
    each: function(fn, scope) {
        _.each(this, fn, scope);
    },

    //to use array.forEach directly
    forEach: function(fn, scope) {
        _.forEach(this, fn, scope);
    }
});

if (typeof Array.prototype.add != 'function') {
    //add is alias for push
    Array.prototype.add = function () {
        this.push.apply(this, arguments);
    };
}

if (typeof Array.prototype.shuffle != 'function') {
    //+ Jonas Raoni Soares Silva
    //@ http://jsfromhell.com/array/shuffle [rev. #1]
    Array.prototype.shuffle = function () {
        for (var j, x, i = this.length; i; j = parseInt(Math.random() * i), x = this[--i], this[i] = this[j], this[j] = x);
        return this;
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
