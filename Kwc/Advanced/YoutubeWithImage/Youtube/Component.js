Kwf.onJElementReady('.cssClass', function youtubePlayer(el) {
    var youtubeApiLoaded = false;
    var animationFinished = false;
    var imageEl = el.children('.image');
    var youtubeContainerEl = el.children('.youtubeContainer');
    var youtubePlayerEl = youtubeContainerEl.children('.youtubePlayer');

    imageEl.click(function(ev) {
        Kwf.Utils.YoutubePlayer.load(function() {});
        imageEl.fadeOut(300, function() {
            youtubeContainerEl.css('position', 'relative');
            youtubePlayerEl.show();
            el.addClass('youtubeActive');
            Kwf.callOnContentReady(el.parent());
        });
    });
});
