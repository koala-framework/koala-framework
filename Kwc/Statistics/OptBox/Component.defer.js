var $ = require('jQuery');
var onReady = require('kwf/on-ready');
var cookieOpt = require('kwf/cookie-opt');

onReady.onRender('.kwcClass', function (el, config) {
    if (!cookieOpt.isSetOpt()) {
        if (config.showBanner) {
            setTimeout(function(){
                $('body').addClass('kwfUp-showCookieBanner');
                onReady.callOnContentReady($('body'), { action: 'widthChange' });
            }, 1000);
        }
        el.show();
        el.find('.kwcBem__accept').click(function(e) {
            e.preventDefault();
            cookieOpt.setOpt('in');
            el.hide();
            $('body').removeClass('kwfUp-showCookieBanner');
            onReady.callOnContentReady($('body'), { action: 'widthChange' });
        });
    }
});

cookieOpt.onOptChange(function(value) {
    $('body').find('.kwcClass').hide();
});
