Kwf.Statistics.onCount(function(url, config) {
    if (config.customVariables) {
        for (var x = 0; x < config.customVariables.length; x++) {
            var cv = config.customVariables[x];
            _paq.push(["setCustomVariable", cv.index, cv.name, cv.value, cv.scope]);
        }
    }
    _paq.push(["setCustomUrl", url]);
    _paq.push(["setReferrerUrl", location.href]);
    _paq.push(["trackPageView"]);
});