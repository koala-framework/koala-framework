Ext2.namespace('Kwc.Directories.List.ViewMap');
Kwc.Directories.List.ViewMap.renderedMaps = [];

Kwc.Directories.List.ViewMap.renderMap = function(map) {
    if (Kwc.Directories.List.ViewMap.renderedMaps.indexOf(map) != -1) return;
    Kwc.Directories.List.ViewMap.renderedMaps.push(map);

    var mapContainer = new Ext2.Element(map);
    var cfg = mapContainer.down(".options", true);
    if (!cfg) return;
    cfg = Ext2.decode(cfg.value);

    cfg.mapContainer = mapContainer;
    var cls = eval(cfg.mapClass) || Kwf.GoogleMap.Map;
    var myMap = new cls(cfg);
    map.map = myMap;

    Kwf.GoogleMap.load(function() {
        this.show();
    }, myMap);

    if (cfg.searchFormComponentId) {
        var searchForm = Kwc.Form.formsByComponentId[cfg.searchFormComponentId];
        searchForm.on('beforeSubmit', function(form, ev) {
            myMap.setBaseParams(
                Ext2.applyIf(searchForm.getValues(), myMap.getBaseParams())
            );
            myMap.centerMarkersIntoView();
            return false;
        }, this);
    }
};

Kwf.onElementReady('div.kwcDirectoriesListViewMap', function(map) {
    Kwc.Directories.List.ViewMap.renderMap(map.dom);
}, { checkVisibility: true });
