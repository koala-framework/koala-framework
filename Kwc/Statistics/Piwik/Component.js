Ext.ns('Kwc.Statistics.Piwik');

Kwc.Statistics.Piwik.getTracker = function()
{
    if (!Kwc.Statistics.Piwik.url || !Kwc.Statistics.Piwik.idSite || typeof Piwik == 'undefined') return null;
    try {
        return Piwik.getTracker(Kwc.Statistics.Piwik.url, Kwc.Statistics.Piwik.idSite);
    } catch( err ) {
        return null;
    }
};

Kwf.Statistics.onCount(function(url) {
    var piwikTracker = Kwc.Statistics.Piwik.getTracker();
    if (piwikTracker) {
        piwikTracker.setCustomUrl(url);
        piwikTracker.setReferrerUrl(location.href);
        piwikTracker.trackPageView();
    }
});