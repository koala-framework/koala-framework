var onReady = require('kwf/on-ready');
var $ = require('jQuery');
var ViewAjax = require('kwf/view-ajax/view');

onReady.onRender('.kwcClass', function initViewAjax(el, config) {

    el.find('.kwfUp-kwcDirectoriesListViewAjaxPaging').remove(); //remove paging, we will do endless scrolling instead

    config.el = el.find('.kwcBem__viewContainer')[0];
    el[0].kwcViewAjax = new ViewAjax(config);

    var linkToTop = $('<div class="kwfUp-linkToTop"></div>');
    el.append(linkToTop);
    linkToTop.click(function() {
        window.scrollTo(0, 0);
    });

    $(window).on('scroll', function() {
        var scrollHeight = $(window).scrollTop();
        if (scrollHeight >= 1700) {
            el.addClass('kwfUp-scrolledDown'); //will display linkToTop
        } else {
            el.removeClass('kwfUp-scrolledDown');
        }
    });

}, {
    priority: 0, //call *after* initializing kwcForm to have access to searchForm
    defer: true
});
