Kwf.onJElementShow('.kwcUserLoginFormSuccess', function(el) {
    var url = el.find('input.redirectTo').val();
    if (!url) url = location.href;
    location.href = url;
});