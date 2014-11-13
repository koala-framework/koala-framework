Kwf.onElementReady('.kwcStatisticsOpt .webForm', function(el, config) {
    el.kwcForm.findField('form_opt').el.on('change', function() {
        this.kwcForm.submit();
        if (el.kwcForm.findField('form_opt').getValue()) {
            Kwf.fireComponentEvent('cookieOptChanged', 'in');
        } else {
            Kwf.fireComponentEvent('cookieOptChanged', 'out');
        }
    }, el);
    el.child('.submit').hide();
    Kwf.onComponentEvent('cookieOptChanged', function(value) {
        if (value == 'in') {
            el.kwcForm.findField('form_opt').setValue(true);
        } else if (value == 'out') {
            el.kwcForm.findField('form_opt').setValue(false);
        }
    });
}, {defer: true});
