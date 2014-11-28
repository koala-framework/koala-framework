Kwf.onContentReady(function(readyEl, param) {
    if (!param.newRender) return false;
    Ext.select('.kwcForm > form', true, readyEl).each(function(form) {
        form = form.parent('.kwcForm', false);
        if (form.dom.shopBoxCartInitDone) return;
        form.dom.shopBoxCartInitDone = true;
        form.kwcForm.on('submitSuccess', function(r) {
            Ext.select('.kwcShopBoxCartLink').each(function(el) {
                Ext.Ajax.request({
                    params: { componentId: el.dom.id },
                    url: Kwf.getKwcRenderUrl(),
                    success: function(response, options) {
                        $(this.dom).html($(response.responseText).html());
                        Kwf.callOnContentReady(this.dom, {newRender: true});
                    },
                    scope: el
                });

            }, this);
        }, this);
    });
}, { priority: 0, defer: true }); //call after Kwc.Form.Component
