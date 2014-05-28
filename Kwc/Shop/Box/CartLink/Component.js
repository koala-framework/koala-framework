Kwf.onContentReady(function(readyEl, param) {
    if (!param.newRender) return false;
    Ext2.select('.kwcForm > form', true, readyEl).each(function(form) {
        form = form.parent('.kwcForm', false);
        if (form.shopBoxCartInitDone) return;
        form.shopBoxCartInitDone = true;
        form.kwcForm.on('submitSuccess', function(r) {
            Ext2.select('.kwcShopBoxCartLink').each(function(el) {
                Ext2.Ajax.request({
                    params: { componentId: el.dom.id },
                    url: Kwf.getKwcRenderUrl(),
                    success: function(response, options) {
                        this.replaceWith({ html: response.responseText });
                        Kwf.callOnContentReady(this, {newRender: true});
                    },
                    scope: el
                });

            }, this);
        }, this);
    });
}, window, { priority: 0 }); //call after Kwc.Form.Component
