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
    if (!cfg.markers) {
        cfg.markers = {
            longitude : cfg.longitude,
            latitude  : cfg.latitude,
            autoOpenInfoWindow: true
        };
        if (text) cfg.markers.infoHtml = text.dom.innerHTML;
    }

    var myMap = new Vps.GoogleMap.Map(cfg);

    Vps.GoogleMap.load(function() {
        this.show();
    }, myMap);

    return myMap;
};

Vps.onContentReady(function() {
    var maps = Ext.DomQuery.select('div.vpcAdvancedGoogleMapView');
    Ext.each(maps, function(map) {
        // wenn in vpsSwitchDisplay (Klappbox)
        var switchDisplayUp = Ext.get(map).up('div.vpsSwitchDisplay');
        // wenn in vpsTabs
        var tabsContentUp = Ext.get(map).up('div.vpsTabsContent');

        // TODO: wenn da noch mehr so ausnahmen kommen, könnte man sich eine generelle
        // lösung mit einer 'vpsGmapDelayedRender' cssClass o.Ä. überlegen
        if (switchDisplayUp) {
            (function(switchDisplayUp, map) {
                Ext.get(switchDisplayUp).switchDisplayObject.on('opened', function() {
                    map.gmapObject = Vpc.Advanced.GoogleMap.renderMap(map);
                });
            }).defer(1, this, [switchDisplayUp, map]);
        } else if (tabsContentUp && !tabsContentUp.hasClass('vpsTabsContentActive')) {
            (function(tabsContentUp, map) {
                var tabsUp = Ext.get(tabsContentUp).up('div.vpsTabs');
                Ext.get(tabsUp).tabsObject.on('tabActivate', function(tabs, newIdx, oldIdx) {
                    if (tabsContentUp.dom === tabs.getContentElByIdx(newIdx)) {
                        map.gmapObject = Vpc.Advanced.GoogleMap.renderMap(map);
                    }
                }, Ext.get(tabsUp).tabsObject);
            }).defer(1, this, [tabsContentUp, map]);
        } else {
            map.gmapObject = Vpc.Advanced.GoogleMap.renderMap(map);
        }
    });
});

