var onReady = require('kwf/on-ready');
var youtubeLoader = require('kwf/youtube/loader');

onReady.onRender('.kwcClass .outerYoutubeContainer', function youtubePlayer(el) {
    var youtubeApiLoaded = false;
    var animationFinished = false;
    var imageEl = el.children('.image');
    var youtubeContainerEl = el.children('.youtubeContainer');
    var youtubePlayerEl = youtubeContainerEl.children('.youtubePlayer');
    var loadingEl = youtubeContainerEl.children('.outerLoading');

    imageEl.click(function(ev) {
        youtubeLoader(function() {});
        imageEl.fadeOut(300, function() {
            youtubeContainerEl.css('position', 'relative');
            youtubePlayerEl.show();
            el.addClass('youtubeActive');
            onReady.callOnContentReady(el.parent(), { action: 'show' });
            loadingEl.css('display', 'block');
        });
    });
});
