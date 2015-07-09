Kwf.onJElementReady('.cssClass .outerYoutubeContainer', function youtubePlayer(el) {
    var youtubeApiLoaded = false;
    var animationFinished = false;
    var imageEl = el.children('.image');
    var youtubeContainerEl = el.children('.youtubeContainer');
    var youtubePlayerEl = youtubeContainerEl.children('.youtubePlayer');
    var loadingEl = youtubeContainerEl.children('.outerLoading');


    imageEl.click(function(ev) {
        Kwf.Utils.YoutubePlayer.load(function() {});
        imageEl.fadeOut(300, function() {
            youtubeContainerEl.css('position', 'relative');
            youtubePlayerEl.show();
            el.addClass('youtubeActive');
            Kwf.callOnContentReady(el.parent(), { action: 'show' });
            loadingEl.css('display', 'block');
        });
    });
});
