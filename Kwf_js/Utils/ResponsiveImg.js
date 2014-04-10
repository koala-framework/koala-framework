Kwf.DONT_HASH_TYPE_PREFIX = 'dh-';

Kwf.Utils._lastScrollTop = null;
$(window).scroll(function()
{
    var $w = $(window);
    if (Kwf.Utils._lastScrollTop && $w.scrollTop()-Kwf.Utils._lastScrollTop < 50) {
        //only check for images to load in steps of 50px, we can do that as we load 50px in advance
        return;
    }

    Kwf.Utils._lastScrollTop = $w.scrollTop()
    for(var i=0; i<Kwf._deferredImages.length; ++i) {
        var el = Kwf._deferredImages[i];
        if (Kwf.Utils._isElementInView(el)) {
            Kwf._deferredImages.splice(i, 1);
            i--;
            Kwf.Utils._initResponsiveImgEl(el);
        }
    }
});


Kwf.Utils._isElementInView = function(el)
{
    var $e = $(el.dom);
    var threshold = 50;
    if ($e.is(":hidden")) return false;
    var $w = $(window);
    var wt = $w.scrollTop(),
        wb = wt + $w.height(),
        et = $e.offset().top,
        eb = et + $e.height();
    return eb >= wt - threshold && et <= wb + threshold;
}

Kwf._deferredImages = [];

Kwf.Utils.ResponsiveImg = function (selector) {
    Kwf.onElementWidthChange(selector, function responsiveImg(el) {
        if (Kwf.Utils._isElementInView(el)) {
            if (!el.responsiveImgInitDone) {
                Kwf.Utils._initResponsiveImgEl(el);
            } else {
                Kwf.Utils._checkResponsiveImgEl(el);
            }
        } else {
            Kwf._deferredImages.push(el);
        }
    }, { defer: true });
};

Kwf.Utils._getResponsiveWidthStep = function (width,  minWidth, maxWidth) {
    var steps = Kwf.Utils._getResponsiveWidthSteps(minWidth, maxWidth);
    for(var i = 0; i < steps.length; i++) {
        if (width <= steps[i]) {
            return steps[i];
        }
    }
    return steps[steps.length-1];
};

// Has similar algorithm in Kwf_Media_Image
Kwf.Utils._getResponsiveWidthSteps = function (minWidth, maxWidth) {
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

Kwf.Utils._initResponsiveImgEl = function (el) {
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

    var width = Kwf.Utils._getResponsiveWidthStep(
            el.loadedWidth * devicePixelRatio, minWidth, maxWidth);
    var sizePath = baseUrl.replace(Kwf.DONT_HASH_TYPE_PREFIX+'{width}',
            Kwf.DONT_HASH_TYPE_PREFIX+width);

    var img = el.child('img', true);
    Ext.fly(img).on('load', function() {
        el.removeClass('webResponsiveImgLoading');
    }, this);
    img.src = sizePath;
};

Kwf.Utils._checkResponsiveImgEl = function (responsiveImgEl) {
    var elWidth = Kwf.Utils.Element.getCachedWidth(responsiveImgEl);
    if (elWidth == 0) return;
    var devicePixelRatio = window.devicePixelRatio ? window.devicePixelRatio : 1;
    var width = Kwf.Utils._getResponsiveWidthStep(elWidth * devicePixelRatio,
            responsiveImgEl.minWidth, responsiveImgEl.maxWidth);
    if (width > responsiveImgEl.loadedWidth) {
        responsiveImgEl.loadedWidth = width;
        responsiveImgEl.child('img', true).src
            = responsiveImgEl.baseUrl.replace(Kwf.DONT_HASH_TYPE_PREFIX+'{width}',
                    Kwf.DONT_HASH_TYPE_PREFIX+width);
    }
};
