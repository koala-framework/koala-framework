var componentEvent = require('kwf/component-event');

onReady.onRender('.kwcClass',function(el) {
    el.find('.kwfUp-formContainer').on('kwfUp-form-submitSuccess', function (event) {
        componentEvent.trigger('kwfUp-shop-addToCart');
    });
});
