var onReady = require('kwf/on-ready');
var $ = require('jQuery');
var getKwcRenderUrl = require('kwf/get-kwc-render-url');
var componentEvent = require('kwf/component-event');

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
