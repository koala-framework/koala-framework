Kwf.onElementReady('.kwcStatisticsOpt .webForm', function(el, config) {
    el.kwcForm.findField('form_opt').el.on('change', function() {
        this.kwcForm.submit();
    }, el);
    el.kwcForm.on('submitSuccess', function () {
        if (el.kwcForm.findField('form_opt').getValue()) {
            Kwf.fireComponentEvent('cookieOptChanged', 'in');
        } else {
            Kwf.fireComponentEvent('cookieOptChanged', 'out');
        }
    }, el);
    Kwf.onComponentEvent('cookieOptChanged', function(value) {
        var field = el.kwcForm.findField('form_opt');
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
}, {defer: true});
