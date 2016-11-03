var onReady = require('kwf/on-ready');
var cookieOpt = require('kwf/cookie-opt');

onReady.onRender('.kwcClass', function(el, config) {
    var checkbox = el.find('input[type="checkbox"]');
    function update(opt) {
        if (opt == 'in') {
            checkbox[0].checked = true;
            var label = config.textOptIn;
        } else {
            checkbox[0].checked = false;
            var label = config.textOptOut;
        }
        el.find('label').html(label);
    }
    cookieOpt.load(function(api) {
        update(api.getOpt());
        checkbox.change(function() {
            api.setOpt(checkbox[0].checked ? 'in' : 'out');
        });
        api.onOptChanged(function(opt) {
            update(opt);
        });
    });
});
