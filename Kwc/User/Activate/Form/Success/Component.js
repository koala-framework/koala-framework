Kwf.onJElementReady('.kwcUserFormSuccess', function(el, config) {
    window.setTimeout(function() {
        window.location.href = config.redirectUrl;
    }, 3000);
});