Kwf.onJElementShow('.kwcUserLoginFormSuccess', function(el) {
    var url = el.find('input.redirectTo').val();
    location.href = url;
});