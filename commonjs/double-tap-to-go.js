var onReady = require('kwf/on-ready');
var $ = require('jQuery');

var isClicked = false;
var lastClickedNode = null;
var visibleNodes = [];
var firstClickedNodes = [];
var touchMoved = false;

function hideVisibleNodes() {
    $.each(visibleNodes, function(i, node) {
        var parentNode = $(node).data('openedTarget');
        $(parentNode).removeClass('hover');
        $(node).removeClass('kwfDoubleTapTargetOpen').data('openedTarget', null);
    });

    visibleNodes = [];

    $(firstClickedNodes).removeClass('kwfDoubleTapFirstClick');
    firstClickedNodes = [];
}
$(document).on('touchmove', function(e) {
    touchMoved = true;
});

$(document).on('touchend', function(e) {
    //ignore if user scrolled
    if (touchMoved) {
        touchMoved = false;
        return;
    }

    //user clicked, close all
    if (!$(e.target).closest('.kwfDoubleTapHandler').length && !$(e.target).hasClass('kwfDoubleTapHandler')) {
        isClicked = false;
        lastClickedNode = false;
        if (visibleNodes.length) {
            hideVisibleNodes();
        }
    }
});

var DoubleTapToGo = function(el, params) {
    params = params || {};

    if (!params.targetToOpen) {
        params.targetToOpen = '.dropdown';
    }

    if (typeof el === 'string') {
        onReady.onRender(el, function(el) {
            DoubleTapToGo(el, params);
        });
        return;
    }

    if ('ontouchstart' in window || navigator.msMaxTouchPoints) {
        $.each(el, function(i, element) {
            if ($(element).hasClass('kwfDoubleTapHandler')) return;

            $(element).addClass('kwfDoubleTapHandler');

            $(element).on('touchend', function(e) {
                e.stopPropagation();

                if (touchMoved) {
                    touchMoved = false;
                    e.preventDefault();
                    return;
                };

                var currentTarget = $(e.currentTarget);

                if (currentTarget.parents('.kwfDoubleTapHandler').length) {

                    var prevAll = currentTarget.prevAll();
                    var nextAll = currentTarget.nextAll();

                    $.each(prevAll, function(i, item) {
                        $(item).removeClass('kwfDoubleTapFirstClick hover');
                        $(item).find('.kwfDoubleTapTargetOpen').removeClass('kwfDoubleTapTargetOpen').data('openedTarget', false);
                        $(item).find('.hover').removeClass('hover');
                        $(item).find('.kwfDoubleTapFirstClick').removeClass('kwfDoubleTapFirstClick');
                    });

                    $.each(nextAll, function(i, item) {
                        $(item).removeClass('kwfDoubleTapFirstClick hover');
                        $(item).find('.kwfDoubleTapTargetOpen').removeClass('kwfDoubleTapTargetOpen').data('openedTarget', false);
                        $(item).find('.hover').removeClass('hover');
                        $(item).find('.kwfDoubleTapFirstClick').removeClass('kwfDoubleTapFirstClick');
                    });

                } else if (!currentTarget.hasClass('kwfDoubleTapFirstClick')) {

                    var parents = currentTarget.parents('.kwfDoubleTapHandler');
                    var hideVisibleElements = true;
                    for (var l = 0; l<parents.length; l++) {
                        if($(parents[l]).hasClass('kwfDoubleTapHandler')) {
                            hideVisibleElements = false;
                        }
                    }

                    if (hideVisibleElements) hideVisibleNodes();
                }

                if (lastClickedNode !== e.currentTarget) {
                    isClicked = false;
                }

                if (!isClicked) {
                    isClicked = true;
                }

                if (isClicked && lastClickedNode !== e.currentTarget) {
                    lastClickedNode = e.currentTarget;
                    var target = currentTarget.find(params.targetToOpen);
                    target.data('openedTarget', e.currentTarget);
                    visibleNodes.push(target);

                    if (target && target.length && !currentTarget.hasClass('kwfDoubleTapFirstClick')) {
                        e.preventDefault();
                        currentTarget.addClass('kwfDoubleTapFirstClick');
                        firstClickedNodes.push(e.currentTarget);
                    }

                    if (!target.hasClass('kwfDoubleTapTargetOpen')) {
                        target.addClass('kwfDoubleTapTargetOpen');
                        currentTarget.addClass('hover');

                        onReady.callOnContentReady(e.currentTarget, {action: 'show'});

                        if (params && params.open) {
                            params.onopen.call(this, e, currentTarget);
                        }
                    }
                }
            });
        })
    }
}

module.exports = DoubleTapToGo;

