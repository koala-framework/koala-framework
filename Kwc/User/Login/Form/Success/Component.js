var onReady = require('kwf/on-ready');

onReady.onShow('.kwcClass', function(el) {
    var url = el.find('input.redirectTo').val();
    if (!url) url = location.href;
    location.href = url;
});
