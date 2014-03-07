Kwf._responsiveImgEls = [];
Kwf._responsiveImgSelectors = [];
Kwf.DONT_HASH_TYPE_PREFIX = 'dh-';

Kwf.Utils.ResponsiveImg = function (selector) {
    Kwf._responsiveImgSelectors.push(selector);
};

Kwf.onContentReady(function(el, options) {
    if (options.newRender) { // maybe new elements so check every selector
        el = Ext.get(el);
        Kwf._responsiveImgSelectors.each(function(i) {
            el.select(i, true).each(function (el) {
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
            });
        });
    } else { // No new elements so iterate over Kwf._responsiveImgEls
        Kwf._responsiveImgEls.each(function(responsiveImgEl) {
            if (el.contains(responsiveImgEl.dom)) {
                if (!responsiveImgEl.responsiveImgInitDone) {
                    if (responsiveImgEl.getWidth() == 0) return;
                    Kwf.Utils._initResponsiveImgEl(responsiveImgEl);
                } else {
                    Kwf.Utils._checkResponsiveImgEl(responsiveImgEl);
                }
            }
        });
    }
});

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
            el.getWidth() * devicePixelRatio, minWidth, maxWidth);
    var sizePath = baseUrl.replace(Kwf.DONT_HASH_TYPE_PREFIX+'{width}',
            Kwf.DONT_HASH_TYPE_PREFIX+width);

    el.createChild({
        tag: 'img',
        src: sizePath
    });
};

Kwf.Utils._checkResponsiveImgEl = function (responsiveImgEl) {
    if (responsiveImgEl.getWidth() == 0) return;
    var devicePixelRatio = window.devicePixelRatio ? window.devicePixelRatio : 1;
    var width = Kwf.Utils._getResponsiveWidthStep(responsiveImgEl.getWidth() * devicePixelRatio,
            responsiveImgEl.minWidth, responsiveImgEl.maxWidth);
    if (width > responsiveImgEl.loadedWidth) {
        responsiveImgEl.loadedWidth = width;
        responsiveImgEl.child('img').dom.src
            = responsiveImgEl.baseUrl.replace(Kwf.DONT_HASH_TYPE_PREFIX+'{width}',
                    Kwf.DONT_HASH_TYPE_PREFIX+width);
    }
};

Ext.fly(window).on('resize', function() {
    Kwf._responsiveImgEls.each(function(responsiveImgEl) {
        Kwf.Utils._checkResponsiveImgEl(responsiveImgEl);
    });
}, this, {buffer: 200});
