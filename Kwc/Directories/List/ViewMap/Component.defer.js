var $ = require('jQuery');
var onReady = require('kwf/on-ready');
var formRegistry = require('kwf/frontend-form/form-registry');
var gmapLoader = require('kwf/google-map/loader');
var gmapMap = require('kwf/google-map/map');

var renderedMaps = [];

var renderMap = function(map) {
    if (renderedMaps.indexOf(map) != -1) return;
    renderedMaps.push(map);

    var cfg = map.find(".options");
    if (!cfg) return;
    cfg = $.parseJSON(cfg.val());

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

Kwf.onJElementReady('.cssClass .mobileOverlay', function (el) {
    el.click(function (ev) {
        if ($(this).is(':visible')) {
            var newEl = $(this).parent();
            newEl.toggleClass('navigate');
            if (newEl.hasClass('navigate')) {
                $('html, body').animate({
                    scrollTop: newEl.offset().top
                });
            } else {
                $('html, body').animate({
                    scrollTop: newEl.offset().top - (($(window).innerHeight() - newEl.height()) / 2)
                });
            }
        }
    });
});
