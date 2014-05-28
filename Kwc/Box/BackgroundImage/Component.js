(function() {

    var checkSize = function() {
        var myWidth = 0, myHeight = 0, windowHeight = 0, windowWidth = 0;

        windowWidth = Ext2.getBody().getViewSize().width;
        windowHeight = Ext2.getBody().getViewSize().height;

        var factorForHeight = backgroundResizeOriginalHeight*100/backgroundResizeOriginalWidth;
        var factorForWidth = backgroundResizeOriginalWidth*100/backgroundResizeOriginalHeight;
        var myHeight = windowWidth/100*factorForHeight;
        var myWidth = windowHeight/100*factorForWidth;

        // For center center effect. To use normal scaling comment the section below
        var marginTop = (myHeight - windowHeight)/2;
        if (marginTop > 0) {
            Ext2.get('background').child('img').setStyle('margin-top', '-'+marginTop+'px');
        }

        if (myHeight >= windowHeight) {
            Ext2.get('background').child('img').setHeight(myHeight);
            Ext2.get('background').child('img').setWidth(windowWidth);
        } else {
           Ext2.get('background').child('img').setHeight(windowHeight);
           Ext2.get('background').child('img').setWidth(myWidth);
        }
    };

    var backgroundResizeOriginalWidth = null;
    var backgroundResizeOriginalHeight = null;

    Ext2.onReady(function() {
        if (Ext2.isIE8) {
            var bgUrl = Ext2.get('background').getStyle('background-image');
            bgUrl = bgUrl.replace(/url\("?(.*?)"?\)/, '$1');

            Ext2.get('background').dom.innerHTML = '<img src="'+bgUrl+'">';
            Ext2.get('background').setStyle('background', 'none');

            Ext2.EventManager.onWindowResize(function() {
                checkSize();
            }, this);
            var image = new Image();
            image.src = bgUrl;

            image.onload = function() {
                backgroundResizeOriginalHeight = image.height;
                backgroundResizeOriginalWidth = image.width;
                checkSize();
            };
            if (image.width) image.onload(); //already loaded, call onload manually
        }
    });

})();
