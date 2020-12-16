var dataLayer = require('kwf/data-layer');

dataLayer.onPush(function (data) {
    window.dataLayer.push(data);
});
