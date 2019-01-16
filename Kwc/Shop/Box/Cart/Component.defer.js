var onReady = require('kwf/commonjs/on-ready');
var getKwcRenderUrl = require('kwf/commonjs/get-kwc-render-url');
var $ = require('jQuery');
var componentEvent = require('kwf/commonjs/component-event');

onReady.onRender('.kwcClass',function(el) {
    componentEvent.on('kwfUp-shop-addToCart', function() {
        $.ajax({
            url: getKwcRenderUrl(),
            data: {componentId: el.data('component-id')},
            success: function (response) {
                el.html($(response).html());
                onReady.callOnContentReady(el, {newRender: true});
            }
        });
    });
});
