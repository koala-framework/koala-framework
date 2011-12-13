Ext.ns('Kwc.Statistics.Piwik');

Kwc.Statistics.Piwik.getTracker = function()
{
    if (!Kwf.Statistics.url || !Kwf.Statistics.id) return null;
    return Piwik.getTracker(Kwf.Statistics.url, Kwf.Statistics.id);
};

Kwf.Statistics.onCount(function(url) {
    var piwikTracker = Kwc.Statistics.Piwik.getTracker();
    piwikTracker.setCustomUrl(url);
    piwikTracker.setReferrerUrl(location.href);
    console.log(url);
    piwikTracker.trackPageView();
});