var onReady = require('kwf/commonjs/on-ready');

Ext2.namespace("Kwc.Box.Dogear");

Kwc.Box.Dogear.initDone = false;

Kwc.Box.Dogear.enlarge = function() {
    var dogearSmall = document.getElementById('dogearSmall');
    var dogearBig = document.getElementById('dogearBig');
    dogearBig.style.top = '0';
    dogearSmall.style.top = '-500px';
};

Kwc.Box.Dogear.shrink = function() {
    var dogearSmall = document.getElementById('dogearSmall');
    var dogearBig = document.getElementById('dogearBig');
    dogearSmall.showLoop();
    dogearSmall.style.top = '0';
    dogearBig.style.top = '-700px';
};

Kwc.Box.Dogear.init = function() {
    Kwc.Box.Dogear.smallDiv.style.display = 'block';
    Kwc.Box.Dogear.bigDiv.style.display = 'block';

    var optionsEl = Ext2.query(".dogearOptions");
    if (!optionsEl) return;
    var options = Ext2.decode(optionsEl[0].value);

    if (options.urlSmall && options.urlBig) {

        var s1 = new swfobject.embedSWF(
            "/assets/kwf/Kwc/Box/DogearRandom/Dogear/dogear.swf",
            "dogearSmall",
            "180","180",
            "9",
            "#FFFFFF",
            {
                'picurl': options.urlSmall,
                'color1': '0x' + options.colors.color_small_1,
                'color2': '0x' + options.colors.color_small_2,
                'linktarget': options.linkOpen ? 'blank' : 'self',
                'clicktag': options.linkUrl
            },
            {
                'allowfullscreen': 'false',
                'allowscriptaccess': 'always',
                'wmode': 'transparent',
                'swLiveConnect': 'true'
            }
        );

        // big image preloaden
        var tmp = new Image();
        tmp.src = options.urlBig;

        var s2 = new swfobject.embedSWF(
            "/assets/kwf/Kwc/Box/DogearRandom/Dogear/dogear_large.swf",
            "dogearBig",
            "680","680",
            "9",
            "#FFFFFF",
            {
                'picurl': options.urlBig,
                'color1': '0x' + options.colors.color_big_1,
                'color2': '0x' + options.colors.color_big_2,
                'linktarget': options.linkOpen ? 'blank' : 'self',
                'clicktag': options.linkUrl
            },
            {
                'allowfullscreen': 'false',
                'allowscriptaccess': 'always',
                'wmode': 'transparent',
                'swLiveConnect': 'true'
            }
        );

        Kwc.Box.Dogear.initDone = true;
    }
};

onReady.onContentReady(function() {
    Kwc.Box.Dogear.smallDiv = document.getElementById('dogearSmall');
    Kwc.Box.Dogear.bigDiv = document.getElementById('dogearBig');

    if (Kwc.Box.Dogear.smallDiv && Kwc.Box.Dogear.bigDiv) {
        if (Ext2.getBody().getWidth() >= 990) {
            Kwc.Box.Dogear.init();
        } else {
            Kwc.Box.Dogear.smallDiv.style.display = 'none';
            Kwc.Box.Dogear.bigDiv.style.display = 'none';
        }

        Ext2.EventManager.addListener(window, 'resize', function() {
            if (Ext2.getBody().getWidth() >= 990) {
                Kwc.Box.Dogear.smallDiv.style.display = 'block';
                Kwc.Box.Dogear.bigDiv.style.display = 'block';
                if (!Kwc.Box.Dogear.initDone) {
                    Kwc.Box.Dogear.init();
                }
            } else {
                Kwc.Box.Dogear.smallDiv.style.display = 'none';
                Kwc.Box.Dogear.bigDiv.style.display = 'none';
            }
        });
    }
});
