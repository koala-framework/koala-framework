var onReady = require('kwf/on-ready');

onReady.onRender('.kwcClass', function(el, config) {
    var innerBackground = el.children('.innerBackground');

    var checkSize = function() {
        var myWidth = 0, myHeight = 0, windowHeight = 0, windowWidth = 0;
        var img = innerBackground.children('img');

        windowWidth = el.parent().width();
        windowHeight = el.parent().height();

        var factorForHeight = backgroundResizeOriginalHeight*100/backgroundResizeOriginalWidth;
        var factorForWidth = backgroundResizeOriginalWidth*100/backgroundResizeOriginalHeight;
        var myHeight = windowWidth/100*factorForHeight;
        var myWidth = windowHeight/100*factorForWidth;

        // For center center effect. To use normal scaling comment the section below
        var marginTop = (myHeight - windowHeight)/2;
        if (marginTop > 0) {
            img.css('margin-top', '-'+marginTop+'px');
        }

        var marginLeft = (myWidth - windowWidth)/2;
        if (marginLeft > 0) {
            img.css('margin-left', '-'+marginLeft+'px');
        } else {
            img.css('margin-left', '0');
        }

        if (myHeight >= windowHeight) {
            img.height(myHeight);
            img.width(windowWidth);
        } else {
            img.height(windowHeight);
            img.width(myWidth);
        }
    };

    var backgroundResizeOriginalWidth = null;
    var backgroundResizeOriginalHeight = null;

    if (!Modernizr.bgsizecover && innerBackground.length) {
        var bgUrl = innerBackground.css('background-image');
        bgUrl = bgUrl.replace(/url\("?(.*?)"?\)/, '$1');

        innerBackground.html('<img src="'+bgUrl+'">');
        innerBackground.css('background', 'none');

        $(window).resize(function() {
            checkSize();
        });
        var image = new Image();
        image.src = bgUrl;

        image.onload = function() {
            backgroundResizeOriginalHeight = image.height;
            backgroundResizeOriginalWidth = image.width;
            checkSize();
            innerBackground.addClass('loaded');
        };
        if (image.width) image.onload(); //already loaded, call onload manually
    }
}, { defer: true });
