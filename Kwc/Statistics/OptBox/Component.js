Kwf.onElementReady('.kwcStatisticsOptBox', function(el, config) {
    if (Kwf.Statistics.getDefaultOptValue() == 'out' && !Kwf.Statistics.issetUserOptValue()) {
        el.slideIn('t', { duration: 2 });
    }
});