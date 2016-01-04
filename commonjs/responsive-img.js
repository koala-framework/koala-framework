var $ = require('jQuery');
var onReady = require('kwf/on-ready');
var isVisible = require('kwf/element/is-visible');
var getCachedWidth = require('kwf/element/get-cached-width');
var _ = require('underscore');

var DONT_HASH_TYPE_PREFIX = 'dh-';
var $w = $(window);
var deferredImages = [];

module.exports = function (selector) {
    onReady.onResize(selector, function responsiveImg(el) {
        if (el[0].responsiveImgInitDone) {
            checkResponsiveImgEl(el);
        } else {
            if (el.hasClass('kwfUp-loadImmediately') || isElementInView(el)) {
                initResponsiveImgEl(el);
            } else {
                if (!el.data('responsiveImgInitDeferred')) {
                    deferredImages.push(el);
                    el.data('responsiveImgInitDeferred', true);
                }
            }
        }
    }, { defer: true });
};

var lastScrollTop = null;
$(function() {
    function showImageWhenVisible()
    {
        if (lastScrollTop && Math.abs($w.scrollTop()-lastScrollTop) < 50) {
            //only check for images to load in steps of 50px, we can do that as we load 50px in advance
            return;
        }

        lastScrollTop = $w.scrollTop()
        for (var i=0; i<deferredImages.length; ++i) {
            var el = deferredImages[i];
            if (isElementInView(el)) {
                deferredImages.splice(i, 1);
                i--;
                initResponsiveImgEl(el);
            }
        }
    }
    $w.scroll(_.debounce(showImageWhenVisible, 150));
});


function getResponsiveWidthStep(width,  minWidth, maxWidth) {
    var steps = getResponsiveWidthSteps(minWidth, maxWidth);
    for (var i = 0; i < steps.length; i++) {
        if (width <= steps[i]) {
            return steps[i];
        }
    }
    return steps[steps.length-1];
};

// Has similar algorithm in Kwf_Media_Image
function getResponsiveWidthSteps(minWidth, maxWidth) {
    var width = minWidth; // startwidth or minwidth
    var steps = [];
    do {
        steps.push(width);
        width += 100;
    } while (width < maxWidth);
    if (width - 100 != maxWidth) {
        steps.push(maxWidth);
    }
    return steps;
};

function initResponsiveImgEl(el) {
    var elWidth = getCachedWidth(el);
    if (elWidth == 0) return;
    el[0].responsiveImgInitDone = true; //don't save as el.data to avoid getting it copied when cloning elements
    var devicePixelRatio = window.devicePixelRatio ? window.devicePixelRatio : 1;
    var baseUrl = el.data("src");
    var minWidth = parseInt(el.data("minWidth"));
    var maxWidth = parseInt(el.data("maxWidth"));

    el.data('baseUrl', baseUrl);
    el.data('minWidth', minWidth);
    el.data('maxWidth', maxWidth);

    var width = getResponsiveWidthStep(
            elWidth * devicePixelRatio, minWidth, maxWidth);
    el.data('loadedWidth', width);
    var sizePath = baseUrl.replace(DONT_HASH_TYPE_PREFIX+'{width}',
            DONT_HASH_TYPE_PREFIX+width);
    var img = $('<img />');
    el.append(img);
    img.on('load', function() {
        el.removeClass('kwfUp-webResponsiveImgLoading');
    });
    img.attr('src', sizePath);
    var noscript = el.find('noscript').text();
    if (noscript) { //doesn't work in ie8
        var title = noscript.match(/title="([^"]+)"/);
        if (title) {
            img.attr('title', title[1]);
        }
        var alt = noscript.match(/alt="([^"]+)"/);
        if (alt) {
            img.attr('alt', alt[1]);
        }
    }
    el.trigger('changesrc', sizePath);
};

function checkResponsiveImgEl(responsiveImgEl) {
    var elWidth = getCachedWidth(responsiveImgEl);
    if (elWidth == 0) return;
    var devicePixelRatio = window.devicePixelRatio ? window.devicePixelRatio : 1;
    var width = getResponsiveWidthStep(elWidth * devicePixelRatio,
            responsiveImgEl.data('minWidth'), responsiveImgEl.data('maxWidth'));
    if (width > responsiveImgEl.data('loadedWidth')) {
        responsiveImgEl.data('loadedWidth', width);
        var sizePath = responsiveImgEl.data('baseUrl').replace(DONT_HASH_TYPE_PREFIX+'{width}',
                    DONT_HASH_TYPE_PREFIX+width);
        responsiveImgEl.find('img').attr('src', sizePath);
        responsiveImgEl.trigger('changesrc', sizePath);
    }
};

function doesElementScroll(el) {
    var i = el.get(0);

    while (i && i != document.body) {
        var overflow = $(i).css('overflow-y');
        if (overflow == 'auto' || overflow == 'scroll') {
            return true;
        }
        i = i.parentNode;
    }
    return false;
}

function isElementInView(el) {
    var threshold = 800;

    if (!isVisible(el[0])) return false;

    if (doesElementScroll(el)) {
        //if img is in a scrolling element always load it.
        //this could be improved but usually it's not needed
        return true;
    }

    var wt = $w.scrollTop(),
        wb = wt + $w.height(),
        et = el.offset().top,
        eb = et + el.innerHeight();
    return eb >= wt - threshold && et <= wb + threshold;
}
