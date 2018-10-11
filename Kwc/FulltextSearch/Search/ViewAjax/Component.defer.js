var onReady = require('kwf/on-ready');
var $ = require('jQuery');
var statistics = require('kwf/statistics');

onReady.onRender('.kwcClass', function (el, config) {

    var getSearchValue = function() {
        var view = el[0].kwcViewAjax;
        if (view && view.searchForm) {
            var seachValues = view.searchForm.getValues();
            if (seachValues.query) {
                return seachValues.query;
            }
        }
    };
    el.find('.kwcBem__viewContainer').on('searchTermEntered', function() {
        var searchValue = getSearchValue();
        if (searchValue) {
            statistics.trackView({internal_search_term: searchValue});
        }
    });
    var searchValue = getSearchValue();
    if (searchValue) {
        statistics.addTrackingData({internal_search_term: searchValue});
    }

}, { priority: 1 });
