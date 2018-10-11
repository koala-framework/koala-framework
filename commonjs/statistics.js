var views = [];
var events = [];
var addTrackingData = [];

var Statistics = {};
Statistics.onView = function(fn) {
    views.push(fn);
};
Statistics.onEvent = function(fn) {
    events.push(fn);
};
Statistics.onAddTrackingData = function(fn) {
    addTrackingData.push(fn);
};

Statistics.count = Statistics.onView;
Statistics.onCount = Statistics.onView;

Statistics.trackView = function(data) {
    views.forEach(function(c) {
        c.call(this, data);
    }, this);
};

Statistics.trackEvent = function(category, action, name, value) {
    events.forEach(function(c) {
        c.call(this, category, action, name, value);
    }, this);
};

Statistics.addTrackingData = function(data) {
    addTrackingData.forEach(function(c) {
        c.call(this, data);
    }, this);
};


module.exports = Statistics;
