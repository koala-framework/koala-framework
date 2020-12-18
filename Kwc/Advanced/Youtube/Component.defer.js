var onReady = require('kwf/on-ready');
var youtubeLoader = require('kwf/youtube/loader');
var $ = require('jQuery');
var statistics = require('kwf/statistics');
var dataLayer = require('kwf/data-layer');

onReady.onHide('.kwcClass .kwcBem__youtubePlayer', function(el) {
    var kwcAdvancedYoutube = el.closest('.kwcClass');
    if (kwcAdvancedYoutube.data('player')) {
        kwcAdvancedYoutube.data('player').pauseVideo();
    }
}, {defer: true});

onReady.onShow('.kwcClass .kwcBem__youtubePlayer', function(el) {
    var kwcAdvancedYoutube = el.closest('.kwcClass');
    var config = kwcAdvancedYoutube.data('config');
    var player = kwcAdvancedYoutube.data('player');
    if (player && config.playerVars.autoplay) {
        if (config.resumeOnShow || player.getPlayerState() != 2) {
            player.playVideo();
        }
    }
}, {defer: true});


onReady.onRender('.kwcClass .kwcBem__youtubePlayer', function(el) {
    var kwcAdvancedYoutube = el.closest('.kwcClass');
    var config = kwcAdvancedYoutube.data('config');
    if (config.videoId) {
        youtubeLoader(function() {
            var loadingEl = kwcAdvancedYoutube.find('.kwcBem__outerLoading');
            loadingEl.css('display', 'none');
            var youtubePlayerEl = $('<div></div>');
            el.append(youtubePlayerEl);

            var player = new YT.Player(youtubePlayerEl.get(0), {
                height: config.height,
                width: config.width,
                videoId: config.videoId,
                playerVars: config.playerVars,
                events: {
                    'onStateChange': function(event, target) {
                        if (event.data == -1) {
                            statistics.trackEvent('Play Video', location.pathname, event.target.getVideoData().title);
                            dataLayer.push({
                                event: "youtube-play",
                                youtube_video_title: event.target.getVideoData().title
                            });
                        }
                    }
                }
            });
            kwcAdvancedYoutube.data('player', player);
            if (config.size == 'custom') {
                kwcAdvancedYoutube.css('width', 'auto');
                kwcAdvancedYoutube.css('max-width', config.width + 'px');
            }
        }, this);
    }
}, {defer: true, checkVisibility: true});
