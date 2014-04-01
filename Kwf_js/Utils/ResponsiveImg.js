Kwf._responsiveImgEls = [];
Kwf._responsiveImgSelectors = [];
Kwf.DONT_HASH_TYPE_PREFIX = 'dh-';

Kwf.Utils.ResponsiveImg = function (selector) {
    //Kwf._responsiveImgSelectors.push(selector);
    Kwf.onElementWidthChange(selector, function initResponsiveImg(el) {
        if (!el.responsiveImgInitDone) {
            // check if this element is already known and therefore in array or not
            if (!el.responsiveImgInArray) {
                el.responsiveImgInArray = true;
                Kwf._responsiveImgEls.push(el);
            }
            if (el.getWidth() == 0) return;
            Kwf.Utils._initResponsiveImgEl(el);
        } else {
            Kwf.Utils._checkResponsiveImgEl(el);
        }
    }, this, { defer: true });
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
    el.responsiveImgInitDone = true;
    var devicePixelRatio = window.devicePixelRatio ? window.devicePixelRatio : 1;
    var baseUrl = el.dom.getAttribute("data-src");
    var minWidth = parseInt(el.dom.getAttribute("data-min-width"));
    var maxWidth = parseInt(el.dom.getAttribute("data-max-width"));

    el.loadedWidth = el.getWidth();
    el.baseUrl = baseUrl;
    el.minWidth = minWidth;
    el.maxWidth = maxWidth;

    var width = Kwf.Utils._getResponsiveWidthStep(
            el.loadedWidth * devicePixelRatio, minWidth, maxWidth);
    var sizePath = baseUrl.replace(Kwf.DONT_HASH_TYPE_PREFIX+'{width}',
            Kwf.DONT_HASH_TYPE_PREFIX+width);

    el.child('img', true).src = sizePath;
};

Kwf.Utils._checkResponsiveImgEl = function (responsiveImgEl) {
    var elWidth = responsiveImgEl.getWidth();
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
