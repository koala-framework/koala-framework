var onReady = require('kwf/on-ready');
var componentEvent = require('kwf/component-event');
var findForm = require('kwf/frontend-form/find-form');

onReady.onRender('.kwcClass', function(el, config) {
    var form = findForm(el);
    form.findField('form_opt').el.change(function() {
        form.submit();
    });
    form.on('submitSuccess', function () {
        if (form.findField('form_opt').getValue()) {
            componentEvent.trigger('cookieOptChanged', 'in');
        } else {
            componentEvent.trigger('cookieOptChanged', 'out');
        }
    });
    componentEvent.on('cookieOptChanged', function(value) {
        var field = form.findField('form_opt');
        if (value == 'in') {
            var fieldValue = true;
            var labelText = trlKwf('Cookies are set when visiting this webpage. Click to deactivate cookies.');
        } else {
            var fieldValue = false;
            var labelText = trlKwf('No cookies are set when visiting this webpage. Click to activate cookies.');
        }
        field.setValue(fieldValue);
        field.el.child('.boxLabel').update(labelText);
    });
});
