Ext.namespace('Kwc.Advanced.GoogleMap');
Kwc.Advanced.GoogleMap.renderedMaps = [];

Kwc.Advanced.GoogleMap.renderMap = function(map) {
    if (Kwc.Advanced.GoogleMap.renderedMaps.indexOf(map) != -1) return;
    Kwc.Advanced.GoogleMap.renderedMaps.push(map);

    var mapContainer = new Ext.Element(map);
    var cfg = mapContainer.down(".options", true);
    if (!cfg) return;
    cfg = Ext.decode(cfg.value);

    var text = mapContainer.down("div.text");
    cfg.mapContainer = mapContainer;
    if (!cfg.markers) {
        cfg.markers = {
            longitude : cfg.longitude,
            latitude  : cfg.latitude,
            autoOpenInfoWindow: true
        };
        if (text) cfg.markers.infoHtml = text.dom.innerHTML;
    }

    var myMap = new Kwf.GoogleMap.Map(cfg);

    Kwf.GoogleMap.load(function() {
        this.show();
    }, myMap);

    return myMap;
};

Kwf.onContentReady(function(el, options) {
    (function(el) {
        var maps = Ext.DomQuery.select('div.kwcAdvancedGoogleMapView', el);
        Ext.each(maps, function(map) {
            if (!map.gmapObject && Ext.get(map).isVisible(true)) {
                map.gmapObject = Kwc.Advanced.GoogleMap.renderMap(map);
            }
        });
    }).defer(1, this, [el]);
});

