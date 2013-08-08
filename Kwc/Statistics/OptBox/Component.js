Kwf.onElementReady('.kwcStatisticsOptBox', function(el, config) {
    if (!Kwf.Statistics.hasOpted()) {
        el.slideIn('t', { duration: 2 });
    }
});