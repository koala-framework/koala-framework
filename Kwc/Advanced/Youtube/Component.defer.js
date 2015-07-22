var onReady = require('kwf/on-ready-ext2');

onReady.onRender('.kwc-class .youtubePlayer', function(el, config) {
    var kwcAdvancedYoutube = el.findParent('.kwc-class', 5, true);
    kwcAdvancedYoutube.dom.config = config;
}, {priority: -1});

onReady.onHide('.kwc-class', function(el) {
    if (el.dom.player) {
        el.dom.player.pauseVideo();
    }
}, {defer: true});

onReady.onShow('.kwc-class .youtubePlayer', function(el) {
    var kwcAdvancedYoutube = el.findParent('.kwc-class', 5, true);
    var config = kwcAdvancedYoutube.dom.config;
    if (kwcAdvancedYoutube.dom.player) {
        if (config.playerVars.autoplay) kwcAdvancedYoutube.dom.player.playVideo();
    } else if (config.videoId) {
        Kwf.Utils.YoutubePlayer.load(function() {
            var loadingEl = kwcAdvancedYoutube.child('.outerLoading');
            loadingEl.enableDisplayMode('block');
            loadingEl.hide();
            var youtubePlayerId = el.createChild().id;

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
    }
}, {defer: true});
