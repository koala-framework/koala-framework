var onReady = require('kwf/on-ready');
var getKwcRenderUrl = require('kwf/get-kwc-render-url');

onReady.onContentReady(function(readyEl, param) {
    if (!param.newRender) return false;
    Ext2.select('.kwfUp-kwcForm > form', true, readyEl).each(function(form) {
        form = form.parent('.kwfUp-kwcForm', false);
        if (form.dom.shopBoxCartInitDone) return;
        form.dom.shopBoxCartInitDone = true;
        form.kwcForm.on('submitSuccess', function(r) {
            Ext2.select('.kwcShopBoxCartLink').each(function(el) {
                Ext2.Ajax.request({
                    params: { componentId: el.dom.id },
                    url: getKwcRenderUrl(),
                    success: function(response, options) {
                        $(this.dom).html($(response.responseText).html());
                        onReady.callOnContentReady(this.dom, {newRender: true});
                    },
                    scope: el
                });

            }, this);
        }, this);
    });
}, { priority: 0 }); //call after Kwc.Form.Component
