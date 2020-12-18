var dataLayer = require('kwf/commonjs/data-layer');

dataLayer.onPush(function (data) {
    window.dataLayer.push(data);
});
