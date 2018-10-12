var onReady = require('kwf/commonjs/on-ready');
var getKwcRenderUrl = require('kwf/commonjs/get-kwc-render-url');
var $ = require('jQuery');

onReady.onRender('.kwcClass',function(el) {
    var url = getKwcRenderUrl();

    $.each($('.kwfUp-addToCardForm .kwfUp-formContainer'), $.proxy(function(index, form) {
        $(form).on('kwfUp-form-submitSuccess', $.proxy(function(event) {
            $.ajax({
                url: url,
                data: { componentId: el.data('component-id') },
                success: function(response) {
                    el.html($(response).html());
                    onReady.callOnContentReady(el, {newRender: true});
                }
            });
        }, el));
    }, el));
}, { priority: 0 }); //call after Kwc.Form.Component
