
Ext.namespace("Vpc.Box.Dogear");

Vpc.Box.Dogear.initDone = false;

Vpc.Box.Dogear.enlarge = function() {
    var dogearSmall = document.getElementById('dogearSmall');
    var dogearBig = document.getElementById('dogearBig');
    dogearBig.style.top = '0';
    dogearSmall.style.top = '-500px';
};

Vpc.Box.Dogear.shrink = function() {
    var dogearSmall = document.getElementById('dogearSmall');
    var dogearBig = document.getElementById('dogearBig');
    dogearSmall.showLoop();
    dogearSmall.style.top = '0';
    dogearBig.style.top = '-700px';
};

Vpc.Box.Dogear.init = function() {
    Vpc.Box.Dogear.smallDiv.style.display = 'block';
    Vpc.Box.Dogear.bigDiv.style.display = 'block';

    var optionsEl = Ext.query(".dogearOptions");
    if (!optionsEl) return;
    var options = Ext.decode(optionsEl[0].value);

    if (options.urlSmall && options.urlBig) {

        var s1 = new swfobject.embedSWF(
            "/assets/vps/Vpc/Box/DogearRandom/Dogear/dogear.swf",
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
            "/assets/vps/Vpc/Box/DogearRandom/Dogear/dogear_large.swf",
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

        Vpc.Box.Dogear.initDone = true;
    }
};

Vps.onContentReady(function() {
    Vpc.Box.Dogear.smallDiv = document.getElementById('dogearSmall');
    Vpc.Box.Dogear.bigDiv = document.getElementById('dogearBig');

    if (Vpc.Box.Dogear.smallDiv && Vpc.Box.Dogear.bigDiv) {
        if (Ext.getBody().getWidth() >= 990) {
            Vpc.Box.Dogear.init();
        } else {
            Vpc.Box.Dogear.smallDiv.style.display = 'none';
            Vpc.Box.Dogear.bigDiv.style.display = 'none';
        }

        Ext.EventManager.addListener(window, 'resize', function() {
            if (Ext.getBody().getWidth() >= 990) {
                Vpc.Box.Dogear.smallDiv.style.display = 'block';
                Vpc.Box.Dogear.bigDiv.style.display = 'block';
                if (!Vpc.Box.Dogear.initDone) {
                    Vpc.Box.Dogear.init();
                }
            } else {
                Vpc.Box.Dogear.smallDiv.style.display = 'none';
                Vpc.Box.Dogear.bigDiv.style.display = 'none';
            }
        });
    }
});
