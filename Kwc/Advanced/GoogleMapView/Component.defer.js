var $ = require('jQuery');
var responsiveEl = require('kwf/responsive-el');
var onReady = require('kwf/on-ready');
var gmapLoader = require('kwf/google-map/loader');
var gmapMap = require('kwf/google-map/map');

responsiveEl('.kwcClass', [500]);

var renderedMaps = [];

var renderMap = function(map) {
    if (renderedMaps.indexOf(map) != -1) return;
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

    var myMap = new gmapMap(cfg);

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
