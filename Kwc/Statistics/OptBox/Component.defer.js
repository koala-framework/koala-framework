var $ = require('jQuery');
var onReady = require('kwf/on-ready');
var cookieOpt = require('kwf/cookie-opt');

onReady.onRender('.kwcClass', function (el, config) {
    if (!cookieOpt.isSetOpt()) {
        el.prepend(config.html);
        $('body').prepend(el);
        el.show();
        el.find('.accept').click(function(e) {
            e.preventDefault();
            cookieOpt.setOpt('in');
            el.hide();
        });
        if (config.silentApprove) {
            cookieOpt.setOpt('in');
        }
    }
});

cookieOpt.onOptChange(function(value) {
    $('body').find('.kwcClass').hide();
});
