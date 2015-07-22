var onReady = require('kwf/on-ready-ext2');
var componentEvent = require('kwf/component-event');

onReady.onRender('.kwc-class .kwfup-webForm', function(el, config) {
    el.kwcForm.findField('form_opt').el.on('change', function() {
        this.kwcForm.submit();
    }, el);
    el.kwcForm.on('submitSuccess', function () {
        if (el.kwcForm.findField('form_opt').getValue()) {
            componentEvent.trigger('cookieOptChanged', 'in');
        } else {
            componentEvent.trigger('cookieOptChanged', 'out');
        }
    }, el);
    componentEvent.on('cookieOptChanged', function(value) {
        if (value == 'in') {
            el.kwcForm.findField('form_opt').setValue(true);
        } else if (value == 'out') {
            el.kwcForm.findField('form_opt').setValue(false);
        }
    });
}, {defer: true});
