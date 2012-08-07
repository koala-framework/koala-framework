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

Kwf.Statistics.onCount(function(url, config) {
    var piwikTracker = Kwc.Statistics.Piwik.getTracker();
    if (piwikTracker) {
        if (config.customVariables) {
            for (var x = 0; x < config.customVariables.length; x++) {
                var cv = config.customVariables[x];
                piwikTracker.setCustomVariable(cv.index, cv.name, cv.value, cv.scope);
            }
        }
        piwikTracker.setCustomUrl(url);
        piwikTracker.setReferrerUrl(location.href);
        piwikTracker.trackPageView();
    }
});