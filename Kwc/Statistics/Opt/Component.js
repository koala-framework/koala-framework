Kwf.onElementReady('.kwcStatisticsOpt .webForm', function(el, config) {
    el.kwcForm.findField('form_opt').el.on('click', function() {
        this.kwcForm.submit();
    }, el);
    el.child('.submit').hide();
});