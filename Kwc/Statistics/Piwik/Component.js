var statistics = require('kwf/statistics');

statistics.onView(function(url) {
    if (typeof(_paq) == 'undefined') return;
    _paq.push(["setCustomUrl", url]);
    _paq.push(["setReferrerUrl", location.href]);
    _paq.push(["trackPageView"]);
});

statistics.onEvent(function(category, action, name, value) {
    if (typeof(_paq) == 'undefined') return;
    _paq.push(["trackEvent", category, action, name, value]);
});