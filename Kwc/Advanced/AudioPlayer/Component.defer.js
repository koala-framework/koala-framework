Kwf.onJElementReady('.cssClass', function(el, config) {
    el.children('audio').mediaelementplayer({
        //custom path to flash
        flashName: '/assets/mediaelement/build/flashmediaelement.swf',
        // if the <audio width> is not specified, this is the default
        defaultAudioWidth: config.defaultAudioWidth,
        // if the <audio height> is not specified, this is the default
        defaultAudioHeight: config.defaultAudioHeight,
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
        // Hide controls when playing and mouse is not over the audio
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
            if (config.autoPlay) {
                mediaElement.play();
            }
        }
    });
}, { checkVisibility: true });
