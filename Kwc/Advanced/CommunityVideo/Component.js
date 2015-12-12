var $ = require('jQuery');
var onReady = require('kwf/on-ready');


onReady.onRender('.kwcClass', function(el, config) {
    el.data('config', config);
}, {priority: -1});

onReady.onResize('.kwcClass', function(el, config) {

    var config = el.data('config');
    var iframe = el.find('iframe');

    if(config.fullWidth) {
        var size = {};
        switch(config.ratio) {
            case "16x9":
                size = {
                    width: $(el).width(),
                    height: ($(el).width() / 16) * 9
                };
                break;
            case "4x3":
                size = {
                    width: $(el).width(),
                    height: ($(el).width() / 4) * 3
                };
                break;
            default:
                return false;
        }
        iframe.width(size.width).height(size.height);
    }

}, {defer: true});


onReady.onHide('.kwcClass', function(el) {
    el.data('iframeHtml', el.find('.communityVideoPlayer').html());
    el.find('iframe').remove();
});

onReady.onShow('.kwcClass', function(el) {
    if(el.data('iframeHtml')) {
        el.find('.communityVideoPlayer').html(el.data('iframeHtml'));
    }
});
