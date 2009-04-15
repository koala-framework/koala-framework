Ext.namespace('Vpc.Directories.List.ViewMap');
Vpc.Directories.List.ViewMap.renderedMaps = [];

Vpc.Directories.List.ViewMap.renderMap = function(map) {
    if (Vpc.Directories.List.ViewMap.renderedMaps.indexOf(map) != -1) return;
    Vpc.Directories.List.ViewMap.renderedMaps.push(map);

    var mapContainer = new Ext.Element(map);
    var cfg = mapContainer.down(".options", true);
    if (!cfg) return;
    cfg = Ext.decode(cfg.value);

    cfg.mapContainer = mapContainer;
    var myMap = new Vps.GoogleMap.Map(cfg);

    Vps.GoogleMap.load(function() {
        this.show();
        var mapTypes = this.gmap.getMapTypes();
        for (var i=0; i<mapTypes.length; i++) {
            mapTypes[i].getMinimumResolution = function() {return 12;}
        }
    }, myMap);
};

Vps.onContentReady(function() {
    var maps = Ext.DomQuery.select('div.vpcDirectoriesListViewMap');
    Ext.each(maps, function(map) {
        Vpc.Directories.List.ViewMap.renderMap(map);
    });
});

