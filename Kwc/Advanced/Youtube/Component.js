Kwf.onElementHide('.kwcAdvancedYoutube', function(el) {
    if (el.dom.player) {
        el.dom.player.destroy();
        el.dom.player = false;
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
        if (config.size == 'custom') {
            kwcAdvancedYoutube.dom.style.width = 'auto';
            kwcAdvancedYoutube.dom.style.maxWidth = config.width + 'px';
        }
    }, this);
}, { defer: true, checkVisibility: true });
