var $ = require('jQuery');
var onReady = require('kwf/on-ready');
var getKwcRenderUrl = require('kwf/get-kwc-render-url');

onReady.onContentReady(function(readyEl, param) {
    if (!param.newRender) return false;

    $('.kwfUp-kwcForm > .kwfUp-formContainer > form').each(function(index, form) {
        form = $(form).parents('.kwfUp-kwcForm');
        if (form.get(0).shopBoxCartInitDone) return;
        form.get(0).shopBoxCartInitDone = true;

        form.get(0).kwcForm.on('submitSuccess', function(r) {
            $('.kwcShopBoxCartLink').each(function(i, el) {
                $.ajax({
                    url: getKwcRenderUrl(),
                    data: {
                        componentId: el.id
                    },
                    success: function (responseText) {
                        $(el).html($(responseText).html());
                        onReady.callOnContentReady(el, {newRender: true});
                    }
                })
            });
        });

    });
}, { priority: 0 }); //call after Kwc.Form.Component
