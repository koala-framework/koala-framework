Kwf.onElementHide('.kwcAdvancedYoutube .youtubePlayer', function(el) {
    var kwcAdvancedYoutube = el.findParent('.kwcAdvancedYoutube', 5, true);
    if (kwcAdvancedYoutube.dom.player) {
        kwcAdvancedYoutube.dom.player.destroy();
        kwcAdvancedYoutube.dom.player = false;
    }
});

Kwf.onElementReady('.kwcAdvancedYoutube .youtubePlayer', function(el, config) {
    var kwcAdvancedYoutube = el.findParent('.kwcAdvancedYoutube', 5, true);
    Kwf.Utils.YoutubePlayer.load(function() {
        var loadingEl = kwcAdvancedYoutube.child('.outerLoading');
        loadingEl.enableDisplayMode('block');
        loadingEl.hide();
        var youtubePlayerId = el.id;

        kwcAdvancedYoutube.dom.player = new YT.Player(youtubePlayerId, {
            height: config.height,
            width: config.width,
            videoId: config.videoId,
            playerVars: config.playerVars
        });
    }, this);
}, { defer: true, checkVisibility: true });
