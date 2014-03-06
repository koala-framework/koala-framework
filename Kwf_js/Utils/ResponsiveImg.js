Kwf._responsiveImgEls = [];
Kwf._responsiveImgSelectors = [];
Kwf.DONT_HASH_TYPE_PREFIX = 'dh-';

Kwf.Utils.ResponsiveImg = function (selector) {
    Kwf._responsiveImgSelectors.push(selector);
    Kwf.onElementReady(selector, function (el) {
        el.isResponsiveImg = true;
        Kwf.Utils._initResponsiveImgEl(el);
    });
};

// onContentReady is needed because onElementReady is only fired once
Kwf.onContentReady(function(el) {
    if (el != document.body) {
        el = Ext.get(el);
        if (el.isResponsiveImg) {
            if (!el.responsiveImgInitDone) {
                Kwf.Utils._initResponsiveImgEl(el);
            } else {
                Kwf.Utils._checkResponsiveImgEl(el.responsiveImgObj, true);
            }
        }
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
    var devicePixelRatio = window.devicePixelRatio ? window.devicePixelRatio : 1;
    if (el.getWidth() == 0) {
        return;
    }
    el.responsiveImgInitDone = true;
    var baseUrl = el.dom.getAttribute("data-src");
    var minWidth = parseInt(el.dom.getAttribute("data-min-width"));
    var maxWidth = parseInt(el.dom.getAttribute("data-max-width"));
    var responsiveImgElement = {
        el: el,
        loadedWidth: el.getWidth(),
        baseUrl: baseUrl,
        minWidth: minWidth,
        maxWidth: maxWidth
    };
    Kwf._responsiveImgEls.push(responsiveImgElement);
    el.responsiveImgObj = responsiveImgElement;

    var width = Kwf.Utils._getResponsiveWidthStep(
            el.getWidth() * devicePixelRatio, minWidth, maxWidth);
    var sizePath = baseUrl.replace(Kwf.DONT_HASH_TYPE_PREFIX+'{width}',
            Kwf.DONT_HASH_TYPE_PREFIX+width);

    el.createChild({
        tag: 'img',
        src: sizePath
    });
};

Kwf.Utils._checkResponsiveImgEl = function (responsiveImgEl, ignoreWidth) {
    var devicePixelRatio = window.devicePixelRatio ? window.devicePixelRatio : 1;
    var width = Kwf.Utils._getResponsiveWidthStep(responsiveImgEl.el.getWidth() * devicePixelRatio,
                    responsiveImgEl.minWidth, responsiveImgEl.maxWidth);
    if (ignoreWidth || width > responsiveImgEl.loadedWidth) {
        responsiveImgEl.loadedWidth = width;
        responsiveImgEl.el.child('img').dom.src
            = responsiveImgEl.baseUrl.replace(Kwf.DONT_HASH_TYPE_PREFIX+'{width}',
                    Kwf.DONT_HASH_TYPE_PREFIX+width);
    }
};

Ext.fly(window).on('resize', function() {
    Kwf._responsiveImgEls.each(function(i) {
        Kwf.Utils._checkResponsiveImgEl(i);
    });
}, this, {buffer: 200});
