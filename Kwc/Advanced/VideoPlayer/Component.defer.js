Kwf.onJElementHide('.kwcAdvancedVideoPlayer', function(el) {
    if (el.get(0).mediaElement) el.get(0).mediaElement.stop();
}, {defer: true});

Kwf.onJElementReady('.kwcAdvancedVideoPlayer', function(el, config) {
    el.find('video').mediaelementplayer({
        //custom path to flash
        flashName: '/assets/mediaelement/build/flashmediaelement.swf',
        // if the <video width> is not specified, this is the default
        defaultVideoWidth: config.defaultVideoWidth,
        // if the <video height> is not specified, this is the default
        defaultVideoHeight: config.defaultVideoHeight,
        // if set, overrides <video width>
        videoWidth: config.videoWidth,
        // if set, overrides <video height>
        videoHeight: config.videoHeight,
        // width of audio player
        audioWidth: config.audioWidth,
        // height of audio player
        audioHeight: config.audioHeight,
        // initial volume when the player starts
        startVolume: config.startVolume,
        // useful for <audio> player loops
        loop: config.loop,
        // enables Flash and Silverlight to resize to content size
        enableAutosize: config.enableAutosize,
        // the order of controls you want on the control bar (and other plugins below)
        features: config.features,
        // Hide controls when playing and mouse is not over the video
        alwaysShowControls: config.alwaysShowControls,
        // force iPad's native controls
        iPadUseNativeControls: config.iPadUseNativeControls,
        // force iPhone's native controls
        iPhoneUseNativeControls: config.iPhoneUseNativeControls,
        // force Android's native controls
        AndroidUseNativeControls: config.AndroidUseNativeControls,
        // forces the hour marker (##:00:00)
        alwaysShowHours: config.alwaysShowHours,
        // show framecount in timecode (##:00:00:00)
        showTimecodeFrameCount: config.showTimecodeFrameCount,
        // used when showTimecodeFrameCount is set to true
        framesPerSecond: config.framesPerSecond,
        // turns keyboard support on and off for this instance
        enableKeyboard: config.enableKeyboard,
        // when this player starts, it will pause other players
        pauseOtherPlayers: config.pauseOtherPlayers,
        // array of keyboard commands
        keyActions: config.keyActions,

        success: function (mediaElement, domObject) {
            el.get(0).mediaElement = mediaElement;
            if (config.autoPlay) {
                mediaElement.play();
            }
        }
    });
});
