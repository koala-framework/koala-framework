var onReady = require('kwf/commonjs/on-ready');
var cookieOpt = require('kwf/commonjs/cookie-opt');
var statistics = require('kwf/commonjs/statistics');

window._paq = [];

onReady.onRender('.kwcClass', function(el, config) {
    if (config.customTrackingDomain) {
        _paq.push(['setCookieDomain', '*.' + config.customTrackingDomain]);
        _paq.push(['setDomains', '*.' + config.customTrackingDomain]);
        _paq.push(['setDocumentTitle', document.domain + "/" + document.title]);
    }
    config.customVariables.forEach(function(cv) {
        _paq.push(['setCustomVariable', cv.index, cv.name, cv.value, cv.scope]);
    });
    config.additionalConfiguration.forEach(function(value, key) {
        _paq.push([key, value]);
    });
    if (config.enableLinkTracking) {
        _paq.push(['enableLinkTracking']);
    }
    _paq.push(['trackPageView']);
    _paq.push(['setSiteId', config.siteId]);

    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://" + config.domain + '/';
    _paq.push(['setTrackerUrl', u+'piwik.php']);

    cookieOpt.load(function(api) {
        if (config.disableCookies || api.getOpt() == 'out') {
            _paq.push(['disableCookies']);
        }
        if (!config.ignore && !location.search.match(/[\?&]kwcPreview/)) {
            (function() {
                var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
                g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
            })();
        }
    })

});

statistics.onView(function(url) {
    _paq.push(['setCustomUrl', url]);
    _paq.push(['setReferrerUrl', location.href]);
    _paq.push(['trackPageView']);
});

statistics.onEvent(function(category, action, name, value) {
    _paq.push(['trackEvent', category, action, name, value]);
});
