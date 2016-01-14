var isLoaded = false;
var isCallbackCalled = false;
var callbacks = [];

module.exports = function(callback, scope)
{
    if (isCallbackCalled) {
        callback.call(scope || window);
        return;
    }
    callbacks.push({
        callback: callback,
        scope: scope
    });
    if (isLoaded) return;

    isLoaded = true;

    //workaround mediaelementjs also defining onYouTubePlayerAPIReady
    //placed here in load() so we are called after mediaelementjs
    var origOnYouTubePlayerAPIReady = window.onYouTubePlayerAPIReady;
    window.onYouTubePlayerAPIReady = function() {
        if (origOnYouTubePlayerAPIReady) origOnYouTubePlayerAPIReady();
        isCallbackCalled = true;
        callbacks.forEach(function(i) {
            i.callback.call(i.scope || window);
        });
    };

    var tag = document.createElement('script');
    tag.setAttribute('type', 'text/javascript');
    tag.setAttribute('src', 'https://www.youtube.com/iframe_api');
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
};
