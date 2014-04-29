Ext.namespace('Kwf.Utils.YoutubePlayer');
Kwf.Utils.YoutubePlayer.isLoaded = false;
Kwf.Utils.YoutubePlayer.isCallbackCalled = false;
Kwf.Utils.YoutubePlayer.callbacks = [];

Kwf.Utils.YoutubePlayer.load = function(callback, scope)
{
    if (Kwf.Utils.YoutubePlayer.isCallbackCalled) {
        callback.call(scope || window);
        return;
    }
    Kwf.Utils.YoutubePlayer.callbacks.push({
        callback: callback,
        scope: scope
    });
    if (Kwf.Utils.YoutubePlayer.isLoaded) return;

    Kwf.Utils.YoutubePlayer.isLoaded = true;

    var tag = document.createElement('script');
    tag.setAttribute('type', 'text/javascript');
    tag.setAttribute('src', 'http://www.youtube.com/iframe_api');
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
};

if (typeof window.onYouTubePlayerAPIReady == 'undefined') {
    window.onYouTubePlayerAPIReady = function() {
        Kwf.Utils.YoutubePlayer.isCallbackCalled = true;
        Kwf.Utils.YoutubePlayer.callbacks.forEach(function(i) {
            i.callback.call(i.scope || window);
        });
    }
}
