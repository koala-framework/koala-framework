var $ = require('jquery');
var onReady = require('kwf/commonjs/on-ready');
var formRegistry = require('kwf/commonjs/frontend-form/form-registry');
var gmapLoader = require('kwf/commonjs/google-map/loader');
var gmapMap = require('kwf/commonjs/google-map/map');

var renderedMaps = [];

var renderMap = function(map) {
    if (renderedMaps.indexOf(map) != -1) return;
    renderedMaps.push(map);

    var cfg = map.find(".options");
    if (!cfg) return;
    cfg = JSON.parse(cfg.val());

    cfg.mapContainer = map;
    var cls = eval(cfg.mapClass) || gmapMap;
    var myMap = new cls(cfg);
    map.map = myMap;

    gmapLoader(function() {
        this.show();
    }, myMap);

    if (cfg.searchFormComponentId) {
        var searchForm = formRegistry.getFormByComponentId(cfg.searchFormComponentId);
        searchForm.on('beforeSubmit', function(form, ev) {
            myMap.setBaseParams($.extend(searchForm.getValues(), myMap.getBaseParams()));
            myMap.centerMarkersIntoView();
            return false;
        }, this);
    }
};

onReady.onRender('.kwcClass', function(map) {
    renderMap(map);
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
