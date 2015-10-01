var onReady = require('kwf/on-ready');
var youtubeLoader = require('kwf/youtube/loader');

onReady.onRender('.kwcClass .kwcBem__outerYoutubeContainer', function youtubePlayer(el) {
    var youtubeApiLoaded = false;
    var animationFinished = false;
    var imageEl = el.children('.kwcBem__image');
    var youtubeContainerEl = el.children('.kwcBem__youtubeContainer');
    var youtubePlayerEl = youtubeContainerEl.children('.kwcBem__youtubePlayer');
    var loadingEl = youtubeContainerEl.children('.kwcBem__outerLoading');

    imageEl.click(function(ev) {
        youtubeLoader(function() {});
        imageEl.fadeOut(300, function() {
            youtubeContainerEl.css('position', 'relative');
            youtubePlayerEl.show();
            el.addClass('kwcBem__youtubeActive');
            onReady.callOnContentReady(el.parent(), { action: 'show' });
            loadingEl.css('display', 'block');
        });
    });
});
