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

Kwf.onContentReady(function() {
    var maps = Ext.DomQuery.select('div.kwcAdvancedGoogleMapView');
    Ext.each(maps, function(map) {
        // wenn in kwfSwitchDisplay (Klappbox)
        var switchDisplayUp = Ext.get(map).up('div.kwfSwitchDisplay');
        // wenn in kwfTabs
        var tabsContentUp = Ext.get(map).up('div.kwfTabsContent');

        // TODO: wenn da noch mehr so ausnahmen kommen, könnte man sich eine generelle
        // lösung mit einer 'kwfGmapDelayedRender' cssClass o.Ä. überlegen
        if (switchDisplayUp) {
            (function(switchDisplayUp, map) {
                Ext.get(switchDisplayUp).switchDisplayObject.on('opened', function() {
                    map.gmapObject = Kwc.Advanced.GoogleMap.renderMap(map);
                });
            }).defer(1, this, [switchDisplayUp, map]);
        } else if (tabsContentUp && !tabsContentUp.hasClass('kwfTabsContentActive')) {
            (function(tabsContentUp, map) {
                var tabsUp = Ext.get(tabsContentUp).up('div.kwfTabs');
                Ext.get(tabsUp).tabsObject.on('tabActivate', function(tabs, newIdx, oldIdx) {
                    if (tabsContentUp.dom === tabs.getContentElByIdx(newIdx)) {
                        map.gmapObject = Kwc.Advanced.GoogleMap.renderMap(map);
                    }
                }, Ext.get(tabsUp).tabsObject);
            }).defer(1, this, [tabsContentUp, map]);
        } else {
            map.gmapObject = Kwc.Advanced.GoogleMap.renderMap(map);
        }
    });
});

