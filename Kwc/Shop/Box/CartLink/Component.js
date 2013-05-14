Kwf.onContentReady(function(readyEl, param) {
    if (!param.newRender) return false;
    Ext.select('.kwcForm > form', true, readyEl).each(function(form) {
        form = form.parent('.kwcForm', false);
        if (form.shopBoxCartInitDone) return;
        form.shopBoxCartInitDone = true;
        form.kwcForm.on('submitSuccess', function(r) {
            Ext.select('.kwcShopBoxCartLink').each(function(el) {
                Ext.Ajax.request({
                    params: { componentId: el.dom.id },
                    url: Kwf.getKwcRenderUrl(),
                    success: function(response, options) {
                        this.replaceWith({ html: response.responseText });
                        Kwf.callOnContentReady();
                    },
                    scope: el
                });

            }, this);
        }, this);
    });
}, window, { priority: 0 }); //call after Kwc.Form.Component
