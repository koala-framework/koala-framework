Kwf.onJElementReady('.kwcUserLoginFormSuccess', function(el) {
    var url = el.find('input.redirectTo').val();
    location.href = url;
}, {checkVisibility: true});