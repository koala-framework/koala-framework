(function(){
    
var DONT_HASH_TYPE_PREFIX = 'dh-';
var $w = $(window);
var deferredImages = [];

Kwf.Utils.ResponsiveImg = function (selector) {
    Kwf.onJElementWidthChange(selector, function responsiveImg(el) {
        if (el.hasClass('loadImmediately') || isElementInView(el)) {
            if (!el[0].responsiveImgInitDone) {
                initResponsiveImgEl(el);
            } else {
                checkResponsiveImgEl(el);
            }
        } else {
            if (!el.data('responsiveImgInitDeferred')) {
                deferredImages.push(el);
                el.data('responsiveImgInitDeferred', true);
            }
        }
    }, { defer: true });
};

var lastScrollTop = null;
$(function() {
    $w.scroll(function()
    {
        if (lastScrollTop && Math.abs($w.scrollTop()-lastScrollTop) < 50) {
            //only check for images to load in steps of 50px, we can do that as we load 50px in advance
            return;
        }

        lastScrollTop = $w.scrollTop()
        for(var i=0; i<deferredImages.length; ++i) {
            var el = deferredImages[i];
            if (isElementInView(el)) {
                deferredImages.splice(i, 1);
                i--;
                initResponsiveImgEl(el);
            }
        }
    });
});


function getResponsiveWidthStep(width,  minWidth, maxWidth) {
    var steps = getResponsiveWidthSteps(minWidth, maxWidth);
    for(var i = 0; i < steps.length; i++) {
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
    var elWidth = Kwf.Utils.Element.getCachedWidth(el);
    if (elWidth == 0) return;
    el[0].responsiveImgInitDone = true; //don't save as el.data to avoid getting it copied when cloning elements
    var devicePixelRatio = window.devicePixelRatio ? window.devicePixelRatio : 1;
    var baseUrl = el.data("src");
    var minWidth = parseInt(el.data("minWidth"));
    var maxWidth = parseInt(el.data("maxWidth"));

    el.loadedWidth = elWidth;
    el.baseUrl = baseUrl;
    el.minWidth = minWidth;
    el.maxWidth = maxWidth;

    var width = getResponsiveWidthStep(
            el.loadedWidth * devicePixelRatio, minWidth, maxWidth);
    var sizePath = baseUrl.replace(DONT_HASH_TYPE_PREFIX+'{width}',
            DONT_HASH_TYPE_PREFIX+width);
    var img = $('<img />');
    el.append(img);
    img.on('load', function() {
        el.removeClass('webResponsiveImgLoading');
    });
    img.attr('src', sizePath);
};

function checkResponsiveImgEl(responsiveImgEl) {
    var elWidth = Kwf.Utils.Element.getCachedWidth(responsiveImgEl);
    if (elWidth == 0) return;
    var devicePixelRatio = window.devicePixelRatio ? window.devicePixelRatio : 1;
    var width = getResponsiveWidthStep(elWidth * devicePixelRatio,
            responsiveImgEl.minWidth, responsiveImgEl.maxWidth);
    if (width > responsiveImgEl.loadedWidth) {
        responsiveImgEl.loadedWidth = width;
        responsiveImgEl.find('img').attr('src',
             responsiveImgEl.baseUrl.replace(DONT_HASH_TYPE_PREFIX+'{width}',
                    DONT_HASH_TYPE_PREFIX+width));
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

    if (!Kwf.Utils.Element.isVisible(el[0])) return false;

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

})();
