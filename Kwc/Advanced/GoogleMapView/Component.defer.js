var $ = require('jQuery');
var onReady = require('kwf/on-ready');
var gmapLoader = require('kwf/google-map/loader');
var gmapMap = require('kwf/google-map/map');



var renderedMaps = [];

var renderMap = function(map) {
    if ($.inArray(map, renderedMaps) != -1) return;
    renderedMaps.push(map);

    var cfg = map.find(".options", true);
    if (!cfg) return;
    cfg = $.parseJSON(cfg.val());

    var text = map.find("div.text");
    cfg.mapContainer = map;
    if (!cfg.markers) {
        cfg.markers = {
            longitude : cfg.longitude,
            latitude  : cfg.latitude,
            autoOpenInfoWindow: cfg.autoOpenInfoWindow
        };
        if (text.length) cfg.markers.infoHtml = text.html();
    }

    var cls = eval(cfg.mapClass) || gmapMap;
    var myMap = new cls(cfg);
    map.map = myMap;

    gmapLoader(function() {
        this.show();
    }, myMap);

    return myMap;
};

onReady.onRender('.kwcClass', function(map) {
    if (!map.get('gmapObject') && !map.is(':hidden')) {
        map.data('gmapObject', renderMap(map));
    }
}, { checkVisibility: true });

onReady.onRender('.kwcClass', function (el) {
    var mobileOverlayOpen = el.find('.mobileOverlayOpen');
    var mobileOverlayClose = el.find('.mobileOverlayClose');

    mobileOverlayOpen.click(function (ev) {
        if ($(this).is(':visible')) {
            var newEl = $(this).parent();
            newEl.addClass('navigate');
            $('html, body').animate({
                scrollTop: newEl.offset().top
            }, 400, function() {
                google.maps.event.trigger(newEl.get(0).map.gmap, 'resize');
            });
        }
    });

    mobileOverlayClose.on('touchstart click', function (ev) {
        if ($(this).is(':visible')) {
            var newEl = $(this).parent();
            newEl.removeClass('navigate');
            $('html, body').animate({
                scrollTop: newEl.offset().top - (($(window).innerHeight() - newEl.height()) / 2)
            });
        }
    });
});
