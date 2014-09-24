Kwf.onJElementReady('.kwcUserLoginFormSuccess', function(el) {
    if (el.is(':visible') && !el.get(0).initDone) {
        el.get(0).initDone = true;
        var url = el.find('input.redirectTo').val();
        location.href = url;
    }
});