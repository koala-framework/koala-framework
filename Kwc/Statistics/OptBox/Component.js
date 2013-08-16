Kwf.onElementReady('.kwcStatisticsOptBox', function(el, config) {
    // TODO: make default behaviour customizable 
    if (Kwf.Statistics.getDefaultOptValue() == 'in' && !Kwf.Statistics.issetUserOptValue()) {
//        el.slideIn('t', { easing: 'linear', duration: 1.25 });
        el.show(true);
    }
});