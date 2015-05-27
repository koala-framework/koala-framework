Kwf.onContentReady(function(readyEl, param) {
    if (!param.newRender) return false;
    Ext2.select('.kwfup-kwcForm > form', true, readyEl).each(function(form) {
        form = form.parent('.kwfup-kwcForm', false);
        if (form.dom.shopBoxCartInitDone) return;
        form.dom.shopBoxCartInitDone = true;
        form.kwcForm.on('submitSuccess', function(r) {
            Ext2.select('.kwcShopBoxCart').each(function(el) {
                Ext2.Ajax.request({
                    params: { componentId: el.dom.id },
                    url: Kwf.getKwcRenderUrl(),
                    success: function(response, options) {
                        this.replaceWith({ html: response.responseText });
                        Kwf.callOnContentReady(this.dom, {newRender: true});
                    },
                    scope: el
                });

            }, this);
        }, this);
    });
}, { priority: 0 }); //call after Kwc.Form.Component
