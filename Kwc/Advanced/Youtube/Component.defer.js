var onReady = require('kwf/on-ready');
var youtubeLoader = require('kwf/youtube/loader');
var $ = require('jQuery');

onReady.onHide('.kwcClass .youtubePlayer', function(el) {
    var kwcAdvancedYoutube = el.closest('.kwcClass');
    if (kwcAdvancedYoutube.data('player')) {
        kwcAdvancedYoutube.data('player').pauseVideo();
    }
}, {defer: true});

onReady.onShow('.kwcClass .youtubePlayer', function(el) {
    var kwcAdvancedYoutube = el.closest('.kwcClass');
    var config = kwcAdvancedYoutube.data('config');
    if (kwcAdvancedYoutube.data('player')) {
        if (config.playerVars.autoplay) kwcAdvancedYoutube.data('player').playVideo();
    } else if (config.videoId) {
        youtubeLoader(function() {
            var loadingEl = kwcAdvancedYoutube.child('.outerLoading');
            loadingEl.css('display', 'nonde');
            var youtubePlayerEl = $('<div></div>');
            el.append(youtubePlayerEl);

            var player = new YT.Player(youtubePlayerEl.get(0), {
                height: config.height,
                width: config.width,
                videoId: config.videoId,
                playerVars: config.playerVars
            });
            kwcAdvancedYoutube.data('player', player);
            if (config.size == 'custom') {
                kwcAdvancedYoutube.dom.style.width = 'auto';
                kwcAdvancedYoutube.dom.style.maxWidth = config.width + 'px';
            }
        }, this);
    }
}, {defer: true});
