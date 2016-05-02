var onReady = require('kwf/on-ready');
var cookieOpt = require('kwf/cookie-opt');
var t = require('kwf/trl');

onReady.onRender('.kwcClass', function(el) {
    var checkbox = el.find('input[type="checkbox"]');
    function update() {
        if (cookieOpt.getOpt() == 'in') {
            checkbox[0].checked = true;
            var label = t.trl('Cookies are set when visiting this webpage. Click to deactivate cookies.');
        } else {
            checkbox[0].checked = false;
            var label = t.trl('No cookies are set when visiting this webpage. Click to activate cookies.');
        }
        el.find('label').html(label);
    }
    update();
    checkbox.change(function() {
        cookieOpt.setOpt(checkbox[0].checked ? 'in' : 'out');
        update();
    });
});
