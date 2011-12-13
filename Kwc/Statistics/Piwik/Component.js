Kwf.Statistics.onCount(function(url) {
    var piwikTracker = Kwf.Statistics.getTracker();
    piwikTracker.setCustomUrl(url);
    piwikTracker.setReferrerUrl(location.href);
    console.log(url);
    piwikTracker.trackPageView();
});