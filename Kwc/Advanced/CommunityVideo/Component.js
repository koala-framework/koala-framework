Kwf.onJElementReady('.kwcAdvancedCommunityVideo', function(el, config) {
    el.data('config', config);
}, {priority: -1});

Kwf.onJElementWidthChange('.kwcAdvancedCommunityVideo', function(el, config) {

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


Kwf.onJElementHide('.kwcAdvancedCommunityVideo', function(el) {
    var iframe = el.find('iframe');
    iframe.parent().data('source', iframe.attr('src'));
    iframe.parent().data('iframeWidth', iframe.attr('width'));
    iframe.parent().data('iframeHeight', iframe.attr('height'));
    iframe.remove();
});

Kwf.onJElementShow('.kwcAdvancedCommunityVideo', function(el) {
    var iframeParent = el.find('.communityVideoPlayer');
    if(iframeParent.data('source')) {
        iframeParent.html('<iframe src="'+iframeParent.data('source')+'" width="'+iframeParent.data('iframeWidth')+'" height="'+iframeParent.data('iframeHeight')+'" frameborder="0" allowfullscreen="true"></iframe>');
    }
});