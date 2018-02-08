var onReady = require('kwf/commonjs/on-ready');
var $ = require('jQuery');

onReady.onRender('.kwcClass', function(el) {
    var form = el.find('form');
    el.find('.submit').click(function(e) {
        e.preventDefault();
        el.find('.kwcBem__process').show();
        form.hide();
        var config = el.data('options');
        $.ajax({
            url: config.controllerUrl,
            data: config.params,
            success: function (responseText) {
                form[0].submit();
            }
        });
    });
});
