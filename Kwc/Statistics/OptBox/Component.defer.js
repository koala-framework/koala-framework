var $ = require('jquery');
var onReady = require('kwf/commonjs/on-ready');
var cookieOpt = require('kwf/commonjs/cookie-opt');

onReady.onRender('.kwcClass', function (el, config) {
    cookieOpt.load(function(api) {
        if (!api.getCookieIsSet()) {
            if (config.showBanner) {
                $('body').addClass('kwfUp-showCookieBanner');
                onReady.callOnContentReady($('body'), { action: 'widthChange' });
            }
            el.show();
            el.find('.kwcBem__accept').click(function(e) {
                e.preventDefault();
                api.setOpt('in');
                el.hide();
                $('body').removeClass('kwfUp-showCookieBanner').addClass('kwfUp-cookieAccepted');
                onReady.callOnContentReady($('body'), { action: 'widthChange' });
            });
        }
        api.onOptChanged(function (value) {
            $('body').find('.kwcClass').hide();
        });
    });
});
