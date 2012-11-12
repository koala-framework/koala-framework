Kwf.onElementReady('.kwcShopCartCheckoutPaymentPayPalConfirmLink', function(el, config) {
    var form = el.child('form');
    form.on('submit', function(e) {
        e.preventDefault();
        Ext.Ajax.request({
            url: config.controllerUrl,
            params: config.params,
            callback: function(response, options) {
                this.dom.submit();
            },
            scope: form
        });
    }, this);
});