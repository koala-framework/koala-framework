var onReady = require('kwf/commonjs/on-ready');
var cookieOpt = require('kwf/commonjs/cookie-opt');
var statistics = require('kwf/commonjs/statistics');

onReady.onRender('.kwcClass', function(el) {
    if (!el.data('ignore-code') && !location.search.match(/[\?&]kwcPreview/)) {
        cookieOpt.load(function(api) {
            if (api.getOpt() != 'out') {
                (function (i, s, o, g, r, a, m) {
                    i['GoogleAnalyticsObject'] = r;
                    i[r] = i[r] || function () {
                            (i[r].q = i[r].q || []).push(arguments)
                        }, i[r].l = 1 * new
                            Date();
                    a = s.createElement(o),
                        m = s.getElementsByTagName(o)[0];
                    a.async = 1;
                    a.src = g;
                    m.parentNode.insertBefore(a, m)
                })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'kwfUp-ga');

                window['kwfUp-ga']('create', el.data('code'), 'auto');
                window['kwfUp-ga']('set', 'anonymizeIp', true);
                window['kwfUp-ga']('send', 'pageview');
            }
        });
    }
});


statistics.onView(function(url) {
    if (typeof(window['kwfUp-ga']) == 'undefined') return;
    window['kwfUp-ga']('send', 'pageview', url);
});

statistics.onEvent(function(category, action, name, value) {
    if (typeof(window['kwfUp-ga']) == 'undefined') return;
    window['kwfUp-ga']('send', 'event', category, action, name, value);
});
