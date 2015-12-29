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
        if (value == 'in') {
            form.findField('form_opt').setValue(true);
        } else if (value == 'out') {
            form.findField('form_opt').setValue(false);
        }
    });
});
