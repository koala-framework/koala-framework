Ext.namespace('Vpc.Directories.List.ViewMap');
Vpc.Directories.List.ViewMap.renderedMaps = [];

Vpc.Directories.List.ViewMap.renderMap = function(map) {
    if (Vpc.Directories.List.ViewMap.renderedMaps.indexOf(map) != -1) return;
    Vpc.Directories.List.ViewMap.renderedMaps.push(map);

    var mapContainer = new Ext.Element(map);
    var cfg = mapContainer.down(".options", true);
    if (!cfg) return;
    cfg = Ext.decode(cfg.value);

    // markers aus partials raus sammeln
    if (!cfg.markers) cfg.markers = [];
    if (!cfg.lightMarkers) cfg.lightMarkers = [];
    var markerEls = mapContainer.query(".markerData");
    markerEls.each(function(mel) {
        var mdata = Ext.decode(mel.value);
        if (typeof cfg.markers == 'object') cfg.markers.push(mdata);
        cfg.lightMarkers.push(mdata);
    });

    // mittelpunkt und zoom für die marker finden
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
        if (!cfg.zoom) {
            cfg.zoom = [ highestLat, highestLng, lowestLat, lowestLng ];
        }
        cfg.longitude = (lowestLng + highestLng) / 2;
        cfg.latitude = (lowestLat + highestLat) / 2;
    }

    cfg.mapContainer = mapContainer;
    var myMap = new Vps.GoogleMap.Map(cfg);

    Vps.GoogleMap.load(function() {
        this.show();
    }, myMap);
};

Vps.onContentReady(function() {
    var maps = Ext.DomQuery.select('div.vpcDirectoriesListViewMap');
    Ext.each(maps, function(map) {
        var up = Ext.get(map).up('div.vpsSwitchDisplay');
        if (up) {
            (function(up, map) {
                Ext.get(up).switchDisplayObject.on('opened', function() {
                    Vpc.Directories.List.ViewMap.renderMap(map);
                });
            }).defer(1, this, [up, map]);
        } else {
            Vpc.Directories.List.ViewMap.renderMap(map);
        }
    });
});
