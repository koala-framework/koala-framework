(function() {

    var checkSize = function() {
        var myWidth = 0, myHeight = 0, windowHeight = 0, windowWidth = 0;

        windowWidth = Ext.getBody().getViewSize().width;
        windowHeight = Ext.getBody().getViewSize().height;

        var factorForHeight = backgroundResizeOriginalHeight*100/backgroundResizeOriginalWidth;
        var factorForWidth = backgroundResizeOriginalWidth*100/backgroundResizeOriginalHeight;
        var myHeight = windowWidth/100*factorForHeight;
        var myWidth = windowHeight/100*factorForWidth;

        // For center center effect. To use normal scaling comment the section below
        var marginTop = (myHeight - windowHeight)/2;
        if (marginTop > 0) {
            Ext.get('background').child('img').setStyle('margin-top', '-'+marginTop+'px');
        }

        if (myHeight >= windowHeight) {
            Ext.get('background').child('img').setHeight(myHeight);
            Ext.get('background').child('img').setWidth(windowWidth);
        } else {
           Ext.get('background').child('img').setHeight(windowHeight);
           Ext.get('background').child('img').setWidth(myWidth);
        }
    };

    var backgroundResizeOriginalWidth = null;
    var backgroundResizeOriginalHeight = null;

    Ext.onReady(function() {
        if (Ext.isIE8 && Ext.get('background')) {
            var bgUrl = Ext.get('background').getStyle('background-image');
            bgUrl = bgUrl.replace(/url\("?(.*?)"?\)/, '$1');

            Ext.get('background').dom.innerHTML = '<img src="'+bgUrl+'">';
            Ext.get('background').setStyle('background', 'none');

            Ext.EventManager.onWindowResize(function() {
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
