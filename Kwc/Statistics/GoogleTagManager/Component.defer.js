var dataLayer = require('kwf/commonjs/data-layer');

dataLayer.onPush(function (data) {
    if (window.dataLayer) window.dataLayer.push(data);
});
