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

Kwf.Statistics.getOptType = function() {
    if (Kwf.Statistics.optType == 'undefined') {
        Kwf.Statistics.optType = 'opt-out';
    }
    return Kwf.Statistics.optType;
};

Kwf.Statistics.hasOpted = function() {
    return Kwf.Statistics.getOptedValue() != null;
};

Kwf.Statistics.isOptedIn = function() {
    if (!Kwf.Statistics.hasOpted()) {
        return Kwf.Statistics.getOptType() == 'opt-out';
    } else {
        return Kwf.Statistics.getOptedValue() == 'in';
    }
};

Kwf.Statistics.getOptedValue = function() {
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
