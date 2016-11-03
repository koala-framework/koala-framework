var onReady = require('kwf/on-ready');

onReady.onRender('.kwcClass', function youtubePlayer(el) {
    var imageEl = el.find('.kwcBem__image');
    var youtubePlayer = el.find('.kwcBem__youtubePlayer');
    var outerYoutubeContainer = el.find('.kwcBem__outerYoutubeContainer');

    imageEl.click(function(ev) {
        imageEl.fadeOut(300, function() {
            outerYoutubeContainer.addClass('youtubeActive');
            onReady.callOnContentReady(youtubePlayer, {action: 'show'});
        });
    });
}, {defer: true});
