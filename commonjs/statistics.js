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
module.exports = Statistics;

