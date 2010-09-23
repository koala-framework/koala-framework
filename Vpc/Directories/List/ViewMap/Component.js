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
