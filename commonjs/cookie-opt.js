var componentEvent = require('kwf/component-event');
var cookies = require('js-cookie');
var $ = require('jQuery');

var CookieOpt = {};

CookieOpt.getDefaultOpt = function() {
    var defaultOpt = $('body').data('cookieDefaultOpt');
    if (defaultOpt != 'in' && defaultOpt != 'out') defaultOpt = 'in';
    return defaultOpt;
};

CookieOpt.isSetOpt = function() {
    return !!cookies.get('cookieOpt');
};

CookieOpt.getOpt = function() {
    if (CookieOpt.isSetOpt()) {
        return cookies.get('cookieOpt');
    } else {
        return CookieOpt.getDefaultOpt();
    }
};

CookieOpt.setOpt = function(value) {
    var opt = cookies.get('cookieOpt');
    cookies.set('cookieOpt', value, { expires: 3*365 });
    if (opt != value) {
        componentEvent.trigger('cookieOptChanged', value);
    }
};

CookieOpt.onOptChange = function(callback) {
    componentEvent.on('cookieOptChanged', callback);
};

module.exports = CookieOpt;
