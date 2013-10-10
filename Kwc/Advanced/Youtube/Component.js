Kwf.onContentReady(function(el){
    Ext.get(el).select('.kwcAdvancedYoutube .youtubePlayer', true).each(function(el) {
        var kwcAdvancedYoutube = el.findParent('.kwcAdvancedYoutube', 5, true);
        if (!el.isVisible(true)) {
            if (kwcAdvancedYoutube.dom.player) {
                kwcAdvancedYoutube.dom.player.destroy();
                kwcAdvancedYoutube.dom.player = false;
            }
            return false;
        } else {
            if (kwcAdvancedYoutube.dom.player) return;
            var config = Ext.decode(el.child('> input[type="hidden"]').getValue());
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
        }
    }, this);
}, this);
