Kwf.onElementReady('.kwcStatisticsOpt .webForm', function(el, config) {
    el.kwcForm.findField('form_opt').el.on('change', function() {
        this.kwcForm.submit();
    }, el);
    el.child('.submit').hide();
}, {defer: true});
