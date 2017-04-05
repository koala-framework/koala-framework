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
        var opt = cookies.get('cookieOpt');
        cookies.set('cookieOpt', value, { expires: 3*365 });
        if (opt != value) {
            for (var i=0; i< onOptChangedCb.length; i++) {
                onOptChangedCb[i].call(this, opt);
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
