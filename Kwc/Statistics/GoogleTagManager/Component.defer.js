var dataLayer = require('kwf/data-layer');

dataLayer.onPush(function (data) {
    if (window.dataLayer) window.dataLayer.push(data);
});
