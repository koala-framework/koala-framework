var statistics = require('kwf/statistics');

statistics.onView(function(url) {
    if (typeof(ga) == 'undefined') return;
    ga('send', 'pageview', url);
});

statistics.onEvent(function(category, action, name, value) {
    if (typeof(ga) == 'undefined') return;
    ga('send', 'event', category, action, name, value);
});
