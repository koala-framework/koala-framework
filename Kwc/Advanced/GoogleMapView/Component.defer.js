Ext2.namespace('Kwc.Advanced.GoogleMap');
Kwc.Advanced.GoogleMap.renderedMaps = [];

Kwc.Advanced.GoogleMap.renderMap = function(map) {
    if (Kwc.Advanced.GoogleMap.renderedMaps.indexOf(map) != -1) return;
    Kwc.Advanced.GoogleMap.renderedMaps.push(map);

    var mapContainer = new Ext2.Element(map);
    var cfg = mapContainer.down(".options", true);
    if (!cfg) return;
    cfg = Ext2.decode(cfg.value);

    var text = mapContainer.down("div.text");
    cfg.mapContainer = mapContainer;
    if (!cfg.markers) {
        cfg.markers = {
            longitude : cfg.longitude,
            latitude  : cfg.latitude,
            autoOpenInfoWindow: cfg.autoOpenInfoWindow
        };
        if (text) cfg.markers.infoHtml = text.dom.innerHTML;
    }

    var cls = eval(cfg.mapClass) || Kwf.GoogleMap.Map;
    var myMap = new cls(cfg);
    map.map = myMap;

    Kwf.GoogleMap.load(function() {
        this.show();
    }, myMap);

    return myMap;
};

Kwf.onContentReady(function(el, options) {
    (function(el) {
        var maps = Ext2.DomQuery.select('div.kwcAdvancedGoogleMapView', el);
        Ext2.each(maps, function(map) {
            if (!map.gmapObject && Ext2.get(map).isVisible(true)) {
                map.gmapObject = Kwc.Advanced.GoogleMap.renderMap(map);
            }
        });
    }).defer(1, this, [el]);
});

Kwf.onJElementReady('.cssClass', function (el) {
    var mobileOverlayOpen = el.find('.mobileOverlayOpen');
    var mobileOverlayClose = el.find('.mobileOverlayClose');

    mobileOverlayOpen.click(function (ev) {
        if ($(this).is(':visible')) {
            var newEl = $(this).parent();
            newEl.addClass('navigate');
            $('html, body').animate({
                scrollTop: newEl.offset().top
            }, 400, function() {
                google.maps.event.trigger(newEl.get(0).map.gmap, 'resize');
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
