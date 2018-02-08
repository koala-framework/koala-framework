var onReady = require('kwf/on-ready');
var $ = require('jQuery');
var getKwcRenderUrl = require('kwf/get-kwc-render-url');

onReady.onRender('.kwcClass',function(el) {
    var url = getKwcRenderUrl();

    $.each($('.kwfUp-addToCardForm .kwfUp-formContainer'), $.proxy(function(index, form) {
        $(form).on('kwfUp-form-submitSuccess', $.proxy(function(event) {
            $.ajax({
                url: url,
                data: { componentId: el.data('component-id') },
                success: function(response) {
                    $('.kwcClass').html(response);
                    onReady.callOnContentReady(el, {newRender: true});
                }
            });
        }, el));
    }, el));
}, { priority: 0 }); //call after Kwc.Form.Component
