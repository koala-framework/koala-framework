Ext.namespace('Vps.GoogleMap');
Vps.GoogleMap.isLoaded = false;
Vps.GoogleMap.isCallbackCalled = false;
Vps.GoogleMap.callbacks = [];

Vps.GoogleMap.load = function(callback, scope)
{
    if (Vps.GoogleMap.isCallbackCalled) {
        callback.call(scope || window);
        return;
    }
    Vps.GoogleMap.callbacks.push({
        callback: callback,
        scope: scope
    });
    if (Vps.GoogleMap.isLoaded) return;

    Vps.GoogleMap.isLoaded = true;

    var url = 'http:/'+'/maps.google.com/maps?file=api&v=2.x&key={Vps_Assets_GoogleMapsApiKey::getKey()}&c&async=2&hl='+trlVps('en');
    url += '&callback=Vps.GoogleMap._loaded';
    var s = document.createElement('script');
    s.setAttribute('type', 'text/javascript');
    s.setAttribute('src', url);
    document.getElementsByTagName("head")[0].appendChild(s);
};

Vps.GoogleMap._loaded = function()
{
    Vps.GoogleMap.isCallbackCalled = true;
    Vps.GoogleMap.callbacks.forEach(function(i) {
        i.callback.call(i.scope || window);
    });
};



Vps.GoogleMap.maps = [];

/**
 * The Vps GoogleMaps object
 *
 * @param config The configuration object that may / must contain the following values
 *     mapContainer (mandatory): The wrapper element of the map (id, dom-element or ext-element).
 *             Must contain a div with class 'container', where the google map itself will be put into-
 *     longitude (optional if coordinates is set): The longitude of the initial center point.
 *     latitude (optional if coordinates is set): The latitude of the initial center point.
 *     coordinates (optional if longitude and latitude is set): The longitude and
 *            latitude of the initial center point. E.g.: '47.802594,13.0433173'
 *     markers (optional): An object of one marker, an array of markers or a an url (string)
 *            to load markers on demand.
 *            Longitude and latitude, or coordinates are mandatory.
 *            See the following example:
 *            [
 *                { longitude: 47.802594, latitude: 13.0433173, infoHtml: 'Text 1' },
 *                { coordinates: '46.1234,12.321', infoHtml: 'Text 2' }
 *            ]
 *     markerSrc (optional): An url to an image that should be used as marker.
 *     lightMarkers (optional): An object of one marker or an array of markers.
 *            Markers at these positions will have another marker color.
 *     lightMarkerSrc (optional): An url to an image that should be used as light marker.
 *     zoom (optional): The initial zoom value. Either an integer or an array of
 *            longitude / latitude values that should be visible in the map.
 *            Default value for zoom is 13.
 *            Example for array usage:
 *            [ top, right, bottom, left ]
 *            - or in coordinates -
 *            [ highest longitude, highest latitude, lowest longitude, lowest latitude ]
 *     width (optional): The width of the map container in pixel. Defaults to 350.
 *     height (optional): The height of the map container in pixel. Defaults to 300.
 *     satelite (optional): 0 or 1, whether it should be possible to switch to satelite
 *            view or not. Defaults to 1.
 *     scale (optional): 0 or 1, whether to show the scale bar or not. Defaults to 1.
 *     zoom_properties (optional): 0 to show large zoom an move controls,
 *            1 to show small zoom an move controls. Defaults to 0.
 *     overview (optional): 0 or 1, whether to show a small overview map at the bottom.
 */
Vps.GoogleMap.Map = function(config) {
    if (!config.mapContainer) throw new Error('config value mapContainer not set');

    this.mapContainer = Ext.get(config.mapContainer);
    this.config = config;
    if (typeof this.config.width == 'undefined') this.config.width = 350;
    if (typeof this.config.height == 'undefined') this.config.height = 300;
    if (typeof this.config.satelite == 'undefined') this.config.satelite = 1;
    if (typeof this.config.scale == 'undefined') this.config.scale = 1;
    if (typeof this.config.zoom_properties == 'undefined') this.config.zoom_properties = 0;
    if (typeof this.config.overview == 'undefined') this.config.overview = 1;
    if (typeof this.config.zoom == 'undefined') this.config.zoom = 13;
    if (typeof this.config.markerSrc == 'undefined') this.config.markerSrc = null;
    if (typeof this.config.lightMarkerSrc == 'undefined') this.config.lightMarkerSrc = '/assets/vps/images/googlemap/markerBlue.png';

    if (!this.config.markers) this.config.markers = [ ];
    if (typeof this.config.markers[0] == 'undefined' &&
        (this.config.markers.longitude || this.config.markers.coordinates)
    ) {
        this.config.markers = [ this.config.markers ];
    }

    for (var i = 0; i < this.config.markers.length; i++) {
        if (this.config.markers[i] && typeof this.config.markers[i].coordinates != 'undefined') {
            if (typeof this.config.markers[i].latitude == 'undefined') {
                var splits = this.config.markers[i].coordinates.split(',');
                this.config.markers[i].latitude = splits[0];
            }
            if (typeof this.config.markers[i].longitude == 'undefined') {
                var splits = this.config.markers[i].coordinates.split(',');
                this.config.markers[i].longitude = splits[1];
            }
        }
    }

    if (!this.config.lightMarkers) this.config.lightMarkers = [ ];
    if (typeof this.config.lightMarkers[0] == 'undefined' &&
        (this.config.lightMarkers.longitude || this.config.lightMarkers.coordinates)
    ) {
        this.config.lightMarkers = [ this.config.lightMarkers ];
    }

    for (var i = 0; i < this.config.lightMarkers.length; i++) {
        if (this.config.lightMarkers[i].coordinates) {
            if (typeof this.config.lightMarkers[i].latitude == 'undefined') {
                var splits = this.config.lightMarkers[i].coordinates.split(',');
                this.config.lightMarkers[i].latitude = splits[0];
            }
            if (typeof this.config.lightMarkers[i].longitude == 'undefined') {
                var splits = this.config.lightMarkers[i].coordinates.split(',');
                this.config.lightMarkers[i].longitude = splits[1];
            }
        }
    }

    if (typeof this.config.coordinates != 'undefined') {
        if (typeof this.config.latitude == 'undefined') {
            var splits = this.config.coordinates.split(',');
            this.config.latitude = splits[0];
        }
        if (typeof this.config.longitude == 'undefined') {
            var splits = this.config.coordinates.split(',');
            this.config.longitude = splits[1];
        }
    }

    if (!this.config.longitude) throw new Error('Either longitude or coordinates must be set in config');
    if (!this.config.latitude) throw new Error('Either latitude or coordinates must be set in config');

    var fromEl = this.mapContainer.down("form.fromAddress");
    if (fromEl) {
        var input = this.mapContainer.down("form.fromAddress input");
        fromEl.on('submit', function(e) {
            this.setMapDir(input.getValue());
            e.stopEvent();
        }, this);
    }

    if (typeof this.config.markers == 'string') {
        if (typeof Vps.Connection == 'undefined') {
            alert('Dependency ExtConnection (that includes Vps.Connection object) must be set when you wish to reload markers in an google map');
        }
        this.ajax = new Vps.Connection({
            autoAbort : true
        });
    }

    var container = this.mapContainer.down(".container");
    container.setWidth(parseInt(this.config.width));
    container.setHeight(parseInt(this.config.height));

};

Vps.GoogleMap.Map.prototype = {

    markers: [ ],

    show : function()
    {
        this.gmap = new GMap2(this.mapContainer.down(".container").dom);

        if (this.config.zoom_properties == '0') {
            this.gmap.addControl(new GLargeMapControl());
        } else if (this.config.zoom_properties == '1') {
            this.gmap.addControl(new GSmallMapControl());
        }

        if (parseInt(this.config.scale)) {
            this.gmap.addControl(
                new GScaleControl(),
                new GControlPosition(G_ANCHOR_BOTTOM_LEFT, new GSize(64,15))
            );
        }
        if (parseInt(this.config.satelite)) {
            this.gmap.addControl(new GMapTypeControl());
        }
        if (parseInt(this.config.overview)) {
            this.gmap.addControl(new GOverviewMapControl());
        }
        if (typeof this.config.zoom_scrollwheel == 'undefined' || this.config.zoom_scrollwheel) {
            this.gmap.enableScrollWheelZoom();
        }

        if (typeof this.config.zoom == 'object'
            && this.config.zoom[0] && this.config.zoom[1]
            && this.config.zoom[2] && this.config.zoom[3]
        ) {
            this.config.zoom = this.gmap.getBoundsZoomLevel(new GLatLngBounds(
                new GLatLng(this.config.zoom[2], this.config.zoom[3]),
                new GLatLng(this.config.zoom[0], this.config.zoom[1])
            ));
            if (this.config.maximumInitialResolution < this.config.zoom)
            	this.config.zoom = this.config.maximumInitialResolution;
        }

        this.gmap.setCenter(
            new GLatLng(
                parseFloat(this.config.latitude),
                parseFloat(this.config.longitude)
            ),
            parseInt(this.config.zoom)
        );

        if (this.mapContainer.down(".mapDir")) {
            this.mapDir = new GDirections(
                this.gmap,
                this.mapContainer.down(".mapDir").dom
            );
        }

        if (typeof this.config.markers == 'string') {
            GEvent.addListener(this.gmap, "moveend", this._reloadMarkers.createDelegate(
                this, [ ]
            ));
            this._reloadMarkers();
        } else {
            this.config.markers.each(function(marker) {
                this.addMarker(marker);
            }, this);
        }

        // Opens the first InfoWindow. Must be deferred, because there were
        // problems opening InfoWindows in multiple maps on one site
        var showNextWindow = function() {
            var map = Vps.GoogleMap.maps.shift();
            if (!map) return;
            map.markers.each(function(m) {
                if (m.vpsConfig.autoOpenInfoWindow) this.showWindow(m);
            }, map);
            if (Vps.GoogleMap.maps.length) {
                showNextWindow.defer(1500, this);
            }
        };
        if (Vps.GoogleMap.maps.length == 0) {
            showNextWindow.defer(1, this);
        }
        Vps.GoogleMap.maps.push(this);

        var mapTypes = this.gmap.getMapTypes();
        var minRes = this.config.minimumResolution;
        var maxRes = this.config.maximumResolution;
        for (var i=0; i<mapTypes.length; i++) {
            if (minRes) {
                mapTypes[i].getMinimumResolution = function() {return minRes;};
            }
            if (maxRes) {
                mapTypes[i].getMaximumResolution = function() {return maxRes;};
            }
        }
    },

    _reloadMarkers: function() {
        var bounds = this.gmap.getBounds();
        var params = { };
        params.lowestLng = bounds.getSouthWest().lng();
        params.lowestLat = bounds.getSouthWest().lat();
        params.highestLng = bounds.getNorthEast().lng();
        params.highestLat = bounds.getNorthEast().lat();

        if (!this.gmapLoader) {
            this.gmapLoader = Ext.getBody().createChild({ tag: 'div', id: 'gmapLoader' });
            this.gmapLoader.dom.innerHTML = trlVps('Loading...');
            this.gmapLoader.alignTo(this.mapContainer, 'tr-tr', [ -10, 50 ]);
        }
        this.gmapLoader.show();

        this.lastReloadMarkersRequestId = this.ajax.request({
            url: this.config.markers,
            success: function(response, options) {
                var ret = Ext.decode(response.responseText);
                ret.markers.each(function(m) {
                    var doAdd = true;
                    for (var i = 0; i < this.markers.length; i++) {
                        if (this.markers[i].vpsConfig.latitude == m.latitude
                            && this.markers[i].vpsConfig.longitude == m.longitude
                        ) {
                            doAdd = false;
                            break;
                        }
                    }
                    if (doAdd) this.addMarker(m);
                }, this);
                this.gmapLoader.hide();
            },
            params: params,
            scope: this
        });
    },

    addMarker : function(markerConfig)
    {
        var gmarkCfg = { draggable: false };
        if (this._isLightMarker(markerConfig.latitude, markerConfig.longitude)
            && this.config.lightMarkerSrc
        ) {
            var lightIcon = new GIcon(G_DEFAULT_ICON);
            lightIcon.image = this.config.lightMarkerSrc;
            gmarkCfg.icon = lightIcon;
        } else if (this.config.markerSrc) {
            var icon = new GIcon(G_DEFAULT_ICON);
            icon.image = this.config.markerSrc;
            gmarkCfg.icon = icon;
        }
        var marker = new GMarker(
            new GLatLng(parseFloat(markerConfig.latitude),
                parseFloat(markerConfig.longitude)),
            gmarkCfg
        );
        marker.vpsConfig = markerConfig;
        this.markers.push(marker);
        this.gmap.addOverlay(marker);

        if (markerConfig.infoHtml) {
            GEvent.addListener(marker, 'click', this.showWindow.createDelegate(
                this, [ marker ]
            ));
        }
    },

    _isLightMarker : function(lat, lng) {
        for (var i = 0; i < this.config.lightMarkers.length; i++) {
            var m = this.config.lightMarkers[i];
            if (m.latitude == lat && m.longitude == lng) {
                return true;
            }
        }
        return false;
    },

    /**
     * @param marker: The marker with 'vpsConfig' property inside
     */
    showWindow : function(marker) {
        if (marker.vpsConfig.infoHtml && marker.vpsConfig.infoHtml != ""
            && "<br />" != marker.vpsConfig.infoHtml.toLowerCase()
        ) {
            marker.openInfoWindowHtml(marker.vpsConfig.infoHtml, {
                maxWidth: parseInt(this.config.width * 0.8)
            });
        }
    },

    setMapDir : function (fromAddress) {
        this.gmap.closeInfoWindow();
        var gcoder = new GClientGeocoder();
        gcoder.setBaseCountryCode('AT');
        gcoder.getLocations(fromAddress, this.testCallback.createDelegate(this));
    },
    testCallback : function(o) {
        if (!o.Placemark) {
            alert(trlVps('Entered place could not been found!'));
        } else {
            this.useFrom(o.Placemark[0], false);
            this.suggestLocations(o.Placemark);
        }
    },
    useFrom : function(Placemark, rewriteInput) {
        if (typeof Placemark != 'object') {
            Placemark = this.suggestPlacemarks[Placemark];
        }
        var pos = Placemark.Point.coordinates[1] +','+ Placemark.Point.coordinates[0];
        this.mapDir.load(
            'from: ' + pos + ' to: ' + this.config.latitude + ',' + this.config.longitude,
            { 'locale': 'de_AT' }
        );
        if (rewriteInput) {
            this.mapContainer.down("form.fromAddress").set({ value: Placemark.address });
        }
        this.mapContainer.down(".mapDirSuggestParent").setStyle({display:"none"});
    },
    suggestLocations : function(Placemark){
        this.suggestPlacemarks = Placemark;
        var el = this.mapContainer.down(".mapDirSuggestParent ul.mapDirSuggest");
        var elParent = this.mapContainer.down(".mapDirSuggestParent");
        if (Placemark.length > 1) {
            el.remove();
            elParent.setStyle({display:"block"});
            el = elParent.createChild({tag: 'ul'});
            el.addClass('mapDirSuggest');
            for (var i=0; i<10; i++) {
                if (!Placemark[i]) break;
                var a = el.createChild({tag: 'li'}).createChild({
                    tag: 'a', href: '#', html: Placemark[i].address, rel:i
                });
                a.on('click', function(e, el) {
                    this.useFrom(Placemark[el.rel], true);
                    e.stopEvent();
                }, this);
            }
        } else if (elParent) {
            elParent.setStyle({ display:"none" });
        }
    }
};
