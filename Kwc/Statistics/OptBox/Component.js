Kwf.onElementReady('.kwcStatisticsOptBox', function(el, config) {
    if (Kwf.Statistics.getDefaultOptValue() == 'out' && !Kwf.Statistics.issetUserOptValue()) {
//        el.slideIn('t', { easing: 'linear', duration: 1.25 });
        el.show(true);
    }
});