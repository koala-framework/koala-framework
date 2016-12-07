var $ = require('jQuery');
var onReady = require('kwf/on-ready');
var gmapLoader = require('kwf/google-map/loader');
var gmapMap = require('kwf/google-map/map');

var renderMap = function(el) {

    var cfg = el.find(".options", true);
    if (!cfg.length) return;
    cfg = $.parseJSON(cfg.val());

    var text = el.find("div.text");
    cfg.mapContainer = el;
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

    gmapLoader(function() {
        this.show();
    }, myMap);

    return myMap;
};

onReady.onRender('.kwcClass', function(el) {
    el.data('gmapObject', renderMap(el));
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
                google.maps.event.trigger(newEl.data('gmapObject').gmap, 'resize');
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
