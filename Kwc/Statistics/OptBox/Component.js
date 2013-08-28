Kwf.onElementReady('.kwcStatisticsOptBox', function(el, config) {
    // TODO: make default behaviour customizable 
    if (Kwf.Statistics.getDefaultOptValue() == 'out' && !Kwf.Statistics.issetUserOptValue()) {
        el.show(true);
    }
});