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

Kwf.Statistics.getDefaultOptValue = function() {
    if (typeof Kwf.Statistics.defaultOptValue == 'undefined') {
        Kwf.Statistics.defaultOptValue = 'opt-out';
    }
    return Kwf.Statistics.defaultOptValue;
};

Kwf.Statistics.issetUserOptValue = function() {
    return Kwf.Statistics.getUserOptValue() != null;
};

Kwf.Statistics.isUserOptIn = function() {
    if (!Kwf.Statistics.issetUserOptValue()) {
        return Kwf.Statistics.getDefaultOptValue() == 'in';
    } else {
        return Kwf.Statistics.getUserOptValue() == 'in';
    }
};

Kwf.Statistics.getUserOptValue = function() {
    var opt = null;
    var cookieName = "cookieOpt=";
    var cookies = document.cookie.split(';');
    for(var i=0; i < cookies.length; i++) {
        var c = cookies[i].trim();
        while (c.charAt(0)==' ') c = c.substring(1, c.length);
        if (c.indexOf(cookieName) == 0) {
            opt = c.substring(cookieName.length, c.length);
        }
    }
    return opt;
};

Kwf.Statistics.setUserOptValue = function(value) {
    if (value == 'in' || value == 'out') {
        var expires = new Date(new Date().getTime() + 3*365*24*60*60);
        document.cookie = 'cookieOpt=' + value + ';path=/;expires=' + expires.toGMTString() + ';';
    }
};
