var onReady = require('kwf/on-ready');
var cookieOpt = require('kwf/cookie-opt');

onReady.onRender('.kwcClass', function(el, config) {
    var checkbox = el.find('input[type="checkbox"]');
    function update() {
        if (cookieOpt.getOpt() == 'in') {
            checkbox[0].checked = true;
            var label = config.textOptIn;
        } else {
            checkbox[0].checked = false;
            var label = config.textOptOut;
        }
        el.find('label').html(label);
    }
    update();
    checkbox.change(function() {
        cookieOpt.setOpt(checkbox[0].checked ? 'in' : 'out');
        update();
    });
});
