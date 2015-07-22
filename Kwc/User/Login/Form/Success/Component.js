var onReady = require('kwf/on-ready');

onReady.onShow('.kwcUserLoginFormSuccess', function(el) {
    var url = el.find('input.redirectTo').val();
    location.href = url;
});
