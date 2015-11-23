Ext2.namespace('Kwc.Directories.List.ViewMap');
Kwc.Directories.List.ViewMap.renderedMaps = [];

Kwc.Directories.List.ViewMap.renderMap = function(map) {
    if (Kwc.Directories.List.ViewMap.renderedMaps.indexOf(map) != -1) return;
    Kwc.Directories.List.ViewMap.renderedMaps.push(map);

    var mapContainer = new Ext2.Element(map);
    var cfg = mapContainer.down(".options", true);
    if (!cfg) return;
    cfg = Ext2.decode(cfg.value);

    // markers aus partials raus sammeln
    if (!cfg.markers) cfg.markers = [];
    if (!cfg.lightMarkers) cfg.lightMarkers = [];
    var markerEls = mapContainer.query(".markerData");
    markerEls.each(function(mel) {
        var mdata = Ext2.decode(mel.value);
        if (typeof cfg.markers == 'object') cfg.markers.push(mdata);
        cfg.lightMarkers.push(mdata);
    });
    if (!cfg.lightMarkers.length) {
        cfg = cfg.concat(cfg.noMarkersOptions);
    }
    // mittelpunkt und zoom für die marker finden
    var useBounds = true;
    if ((!cfg.useZoomPropertyForSingleMarker && cfg.zoom) ||
        (cfg.useZoomPropertyForSingleMarker && cfg.lightMarkers.length <= 1)) {
        useBounds = false;
    }
    if (cfg.lightMarkers.length) {
        var lowestLat = null;
        var highestLat = null;
        var lowestLng = null;
        var highestLng = null;
        cfg.lightMarkers.each(function(lm) {
            if (lowestLng == null || lowestLng > parseFloat(lm.longitude)) {
                lowestLng = parseFloat(lm.longitude);
            }
            if (highestLng == null || highestLng < parseFloat(lm.longitude)) {
                highestLng = parseFloat(lm.longitude);
            }
            if (lowestLat == null || lowestLat > parseFloat(lm.latitude)) {
                lowestLat = parseFloat(lm.latitude);
            }
            if (highestLat == null || highestLat < parseFloat(lm.latitude)) {
                highestLat = parseFloat(lm.latitude);
            }
        });
    }
    if (lowestLng && highestLng && lowestLat && highestLat) {
        if (useBounds) {
            cfg.zoom = [ highestLat, highestLng, lowestLat, lowestLng ];
        }
        if (!cfg.longitude) {
            cfg.longitude = (lowestLng + highestLng) / 2;
        }
        if (!cfg.latitude) {
            cfg.latitude = (lowestLat + highestLat) / 2;
        }
    }

    cfg.mapContainer = mapContainer;
    var cls = eval(cfg.mapClass) || Kwf.GoogleMap.Map;
    var myMap = new cls(cfg);
    map.map = myMap;

    Kwf.GoogleMap.load(function() {
        this.show();
    }, myMap);
};

Kwf.onElementReady('div.kwcDirectoriesListViewMap', function(map) {
    Kwc.Directories.List.ViewMap.renderMap(map.dom);
}, { checkVisibility: true });

Kwf.onJElementReady('.cssClass', function (el) {
    var mobileOverlayOpen = el.find('.mobileOverlayOpen');
    var mobileOverlayClose = el.find('.mobileOverlayClose');

    mobileOverlayOpen.click(function (ev) {
        if ($(this).is(':visible')) {
            var newEl = $(this).parent();
            newEl.addClass('navigate');
            $('html, body').animate({
                scrollTop: newEl.offset().top
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
