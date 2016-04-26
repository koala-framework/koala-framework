var views = [];
var events = [];

var Statistics = {};
Statistics.onView = function(fn) {
    views.push(fn);
};
Statistics.onEvent = function(fn) {
    events.push(fn);
};

Statistics.count = Statistics.onView;
Statistics.onCount = Statistics.onView;

Statistics.trackView = function(url) {
    views.forEach(function(c) {
        c.call(this, url);
    }, this);
};

Statistics.trackEvent = function(category, action, name, value) {
    events.forEach(function(c) {
        c.call(this, category, action, name, value);
    }, this);
};

Statistics.getDefaultOptValue = function() {
    if (typeof Statistics.defaultOptValue == 'undefined') {
        Statistics.defaultOptValue = 'in';
    }
    return Statistics.defaultOptValue;
};

Statistics.issetUserOptValue = function() {
    return Statistics.getUserOptValue() != null;
};

Statistics.isUserOptIn = function() {
    if (!Statistics.issetUserOptValue()) {
        return Statistics.getDefaultOptValue() == 'in';
    } else {
        return Statistics.getUserOptValue() == 'in';
    }
};

Statistics.getUserOptValue = function() {
    var opt = null;
    var cookieName = "cookieOpt=";
    var cookies = document.cookie.split(';');
    for(var i=0; i < cookies.length; i++) {
        var c = cookies[i].replace(/^\s+|\s+$/g, '');
        while (c.charAt(0)==' ') c = c.substring(1, c.length);
        if (c.indexOf(cookieName) == 0) {
            opt = c.substring(cookieName.length, c.length);
        }
    }
    return opt;
};

Statistics.setUserOptValue = function(value) {
    if (value == 'in' || value == 'out') {
        var expires = new Date(new Date().getTime() + 3*365*24*60*60);
        document.cookie = 'cookieOpt=' + value + ';path=/;expires=' + expires.toGMTString() + ';';
    }
};

module.exports = Statistics;

