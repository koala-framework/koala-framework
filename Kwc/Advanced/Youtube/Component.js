Kwf.onContentReady(function(el){
    Ext.get(el).select('.kwcAdvancedYoutube .youtubePlayer', true).each(function(el) {
        if (!el.isVisible(true)) {
            el = el.findParent('.kwcAdvancedYoutube', 5, true);
            if (el.dom.player) el.dom.player.pauseVideo();
            return;
        }
    }, this);
}, this);

Kwf.onElementReady('.kwcAdvancedYoutube .youtubePlayer', function(el, config) {
    var kwcAdvancedYoutube = el.findParent('.kwcAdvancedYoutube', 5, true);
    if (kwcAdvancedYoutube.dom.player) return;
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
}, this, { checkVisibility: true });
