Ext.namespace('Vpc.Advanced.GoogleMap');
Vpc.Advanced.GoogleMap.renderedMaps = [];

Vpc.Advanced.GoogleMap.renderMap = function(map) {
    if (Vpc.Advanced.GoogleMap.renderedMaps.indexOf(map) != -1) return;
    Vpc.Advanced.GoogleMap.renderedMaps.push(map);

    var mapContainer = new Ext.Element(map);
    var cfg = mapContainer.down(".options", true);
    if (!cfg) return;
    cfg = Ext.decode(cfg.value);

    var text = mapContainer.down("div.text");
    cfg.mapContainer = mapContainer;
    cfg.markers = {
        longitude : cfg.longitude,
        latitude  : cfg.latitude,
        autoOpenInfoWindow: true
    };
    if (text) cfg.markers.infoHtml = text.dom.innerHTML;

    var myMap = new Vps.GoogleMap.Map(cfg);

    Vps.GoogleMap.load(function() {
        this.show();
    }, myMap);
};

Vps.onContentReady(function() {
    var maps = Ext.DomQuery.select('div.vpcAdvancedGoogleMapView');
    Ext.each(maps, function(map) {
        var up = Ext.get(map).up('div.vpsSwitchDisplay');
        if (up) {
            (function(up, map) {
                Ext.get(up).switchDisplayObject.on('opened', function() {
                    Vpc.Advanced.GoogleMap.renderMap(map);
                });
            }).defer(1, this, [up, map]);
        } else {
            Vpc.Advanced.GoogleMap.renderMap(map);
        }
    });
});

