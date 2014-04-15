(function(){

var DONT_HASH_TYPE_PREFIX = 'dh-';
var $w = $(window);
var deferredImages = [];

Kwf.Utils.ResponsiveImg = function (selector) {
    Kwf.onElementWidthChange(selector, function responsiveImg(el) {
        if (isElementInView(el)) {
            if (!el.responsiveImgInitDone) {
                initResponsiveImgEl(el);
            } else {
                checkResponsiveImgEl(el);
            }
        } else {
            deferredImages.push(el);
        }
    }, { defer: true });
};

var lastScrollTop = null;
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
    el.responsiveImgInitDone = true;
    var devicePixelRatio = window.devicePixelRatio ? window.devicePixelRatio : 1;
    var baseUrl = el.dom.getAttribute("data-src");
    var minWidth = parseInt(el.dom.getAttribute("data-min-width"));
    var maxWidth = parseInt(el.dom.getAttribute("data-max-width"));

    el.loadedWidth = elWidth;
    el.baseUrl = baseUrl;
    el.minWidth = minWidth;
    el.maxWidth = maxWidth;

    var width = getResponsiveWidthStep(
            el.loadedWidth * devicePixelRatio, minWidth, maxWidth);
    var sizePath = baseUrl.replace(DONT_HASH_TYPE_PREFIX+'{width}',
            DONT_HASH_TYPE_PREFIX+width);

    var img = el.child('img', true);
    Ext.fly(img).on('load', function() {
        el.removeClass('webResponsiveImgLoading');
    }, this);
    img.src = sizePath;
};

function checkResponsiveImgEl(responsiveImgEl) {
    var elWidth = Kwf.Utils.Element.getCachedWidth(responsiveImgEl);
    if (elWidth == 0) return;
    var devicePixelRatio = window.devicePixelRatio ? window.devicePixelRatio : 1;
    var width = getResponsiveWidthStep(elWidth * devicePixelRatio,
            responsiveImgEl.minWidth, responsiveImgEl.maxWidth);
    if (width > responsiveImgEl.loadedWidth) {
        responsiveImgEl.loadedWidth = width;
        responsiveImgEl.child('img', true).src
            = responsiveImgEl.baseUrl.replace(DONT_HASH_TYPE_PREFIX+'{width}',
                    DONT_HASH_TYPE_PREFIX+width);
    }
};

function isElementInView(el)
{
    var $e = $(el.dom);
    var threshold = 50;
    
    if ($e.is(":hidden")) return false;
    var wt = $w.scrollTop(),
        wb = wt + $w.height(),
        et = $e.offset().top,
        eb = et + el.dom.clientWidth;
    return eb >= wt - threshold && et <= wb + threshold;
}

})();
