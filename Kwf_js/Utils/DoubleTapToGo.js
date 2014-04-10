Ext.ns('Kwf.Utils');
/*
    By Osvaldas Valutis, www.osvaldas.info
    Available for use under the MIT License
*/
Kwf.Utils.DoubleTapToGo = function(el, params) {
    if (!('ontouchstart' in window) &&
        !navigator.msMaxTouchPoints &&
        !navigator.userAgent.toLowerCase().match( /windows phone os 7/i )
    ) return false;

    var applyOnEl = function(el, params) {
        Kwf.log(el);
        var curItem = false;
        $(el).on('click', function(e) {
            var item = $(this);
            if(item[0] != curItem[0]) {
                e.preventDefault();
                curItem = item;
            }
        });

        $(document).on('click touchstart MSPointerDown', function(e) {
            if (params && params.onopen && !el[0].doubleTapToGo) {
                el[0].doubleTapToGo = true;
                params.onopen.call(this, e, el[0]);
            }

            var resetItem = true;
            var parents = $( e.target ).parents();
            for (var i = 0; i < parents.length; i++) {
                if (parents[i] == curItem[0]) {
                    resetItem = false;
                }
            }
            if (resetItem) curItem = false;
        });
    };

    if (el instanceof jQuery) {
        applyOnEl(el, params);
    } else {
        Kwf.onJElementReady(el, Kwf.Utils.DoubleTapToGo);
    }
};
