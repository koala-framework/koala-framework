Ext.ns('Kwc.Statistics.Piwik');

Kwc.Statistics.Piwik.getTracker = function()
{
    if (!Kwc.Statistics.Piwik.url || !Kwc.Statistics.Piwik.idSite) return null;
    return Piwik.getTracker(Kwc.Statistics.Piwik.url, Kwc.Statistics.Piwik.idSite);
};

Kwf.Statistics.onCount(function(url) {
    var piwikTracker = Kwc.Statistics.Piwik.getTracker();
    piwikTracker.setCustomUrl(url);
    piwikTracker.setReferrerUrl(location.href);
    piwikTracker.trackPageView();
});