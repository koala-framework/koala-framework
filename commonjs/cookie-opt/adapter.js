var $ = require('jquery');
var componentEvent = require('kwf/commonjs/component-event');
var cookies = require('js-cookie');

var onOptChangedCb = [];
var api = {
    getDefaultOpt: function() {
        var defaultOpt = $('body').data('cookieDefaultOpt');
        if (defaultOpt != 'in' && defaultOpt != 'out') defaultOpt = 'in';
        return defaultOpt;
    },

    getCookieIsSet: function() {
        return !!cookies.get('cookieOpt');
    },

    getOpt: function() {
        if (api.getCookieIsSet()) {
            return cookies.get('cookieOpt');
        } else {
            return api.getDefaultOpt();
        }
    },

    setOpt: function(value) {
        var oldValue = cookies.get('cookieOpt');
        cookies.set('cookieOpt', value, { expires: 3*365 });
        if (oldValue != value) {
            for (var i=0; i< onOptChangedCb.length; i++) {
                onOptChangedCb[i].call(this, value);
            }
        }
    },

    onOptChanged: function(callback) {
        onOptChangedCb.push(callback);
    }
};

var getAdapter = function(deferred) {
    deferred.resolve(api);
};

module.exports = getAdapter;
