var $ = require('jquery');
var t = require('kwf/commonjs/trl');
var onReady = require('kwf/commonjs/on-ready');

/**
 * The Kwf GoogleMaps object
 *
 * @deprecated DO NOT USE THIS CLASS. Rather use the loader (loader.js) and the Google Maps API itself.
 *
 * @param config The configuration object that may / must contain the following values
 *     mapContainer (mandatory): The wrapper element of the map (id, dom-element or jquery-element).
 *             Must contain a div with class 'container', where the google map itself will be put into.
 *     width (optional): The width of the map container in pixel. Defaults to 350.
 *     height (optional): The height of the map container in pixel. Defaults to 300.
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
 *     singleMarkerZoom (optional): Zoom if only one marker is set. Defaults to 15.
 *     zoomControl (optional): true to show large zoom controls. Defaults to true.
 *     zoomControlPosition (optional): Identifiers used to specify the placement of controls on the map.
 *     mapTypeControl: true to show map type controls. Defaults to true.
 *     mapType (optional): Type of map ('roadmap', 'satellite', 'hybrid', 'terrain'). Defaults to 'roadmap'.
 *     streetViewControl (optional): boolean: true if StreetViewControl should be enabled. Defaults to false.
 *     clickableIcons (optional): boolean: true if POIs should be clickable. Defaults to false.
 *     scrollWheel (optional): boolean: If false, disables zooming on the map using a mouse scroll wheel. Defaults to true.
 */
var Map = function(config) {
    if (!config.mapContainer) throw new Error('config value mapContainer not set');

    this.mapContainer = $(config.mapContainer);
    //this.mapContainer = Ext2.get(config.mapContainer);
    this._baseParams = $.extend({}, config.baseParams);
    this.markers = [];
    this.config = config;

    if (typeof this.config.width == 'undefined') this.config.width = 350;
    if (typeof this.config.height == 'undefined') this.config.height = 300;
    if (typeof this.config.mapTypeControl == 'undefined') this.config.mapTypeControl = true;
    if (typeof this.config.mapType == 'undefined') this.config.mapType = 'roadmap';
    if (typeof this.config.zoomControl == 'undefined') this.config.zoomControl = true;
    if (typeof this.config.zoom == 'undefined') this.config.zoom = 13;
    if (typeof this.config.markerSrc == 'undefined') this.config.markerSrc = null;
    if (typeof this.config.singleMarkerZoom == 'undefined') this.config.singleMarkerZoom = 15;
    if (typeof this.config.lightMarkerSrc == 'undefined') this.config.lightMarkerSrc = '/assets/kwf/images/googlemap/markerBlue.png';
    if (typeof this.config.scrollwheel == 'undefined') this.config.scrollwheel = true;
    if (typeof this.config.zoomControlPosition == 'undefined') this.config.zoomControlPosition = 'LEFT_TOP';
    if (typeof this.config.streetViewControl == 'undefined') this.config.streetViewControl = false;
    if (typeof this.config.clickableIcons == 'undefined') this.config.clickableIcons = false;

    if (!this.config.markers) this.config.markers = [ ];
    if (typeof this.config.markers[0] == 'undefined' &&
        (this.config.markers.longitude || this.config.markers.coordinates)
    ) {
        this.config.markers = [ this.config.markers ];
    }

    if (typeof this.config.markers == 'object') {
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

    var fromEl = this.mapContainer.find("form.fromAddress");
    if (fromEl) {
        var input = this.mapContainer.find("form.fromAddress input");
        fromEl.on('submit', (function(ev) {
            ev.preventDefault();
            this.setMapDir(input.val());
        }).bind(this));
    }

    var container = this.mapContainer.find(".container");
    container.width(this.config.width);
    container.height(this.config.height);
};

Map.prototype = {

    markers: null,
    show : function()
    {
        this.directionsService = new google.maps.DirectionsService();
        this.directionsDisplay = new google.maps.DirectionsRenderer();
        var mapOptions = {
            center: new google.maps.LatLng(parseFloat(this.config.latitude), parseFloat(this.config.longitude)),
            zoom: (typeof this.config.zoom == 'number')? parseInt(this.config.zoom) : 13, //initial zoom has to be set
            zoomControl: this.config.zoomControl,
            zoomControlOptions: {
                position: google.maps.ControlPosition[this.config.zoomControlPosition]
            },
            streetViewControl: this.config.streetViewControl,
            gestureHandling: this.config.scrollwheel ? "greedy" : "cooperative",
            mapTypeControl: this.config.mapTypeControl,
            mapTypeId: this.config.mapType,
            clickableIcons: this.config.clickableIcons
        };
        if (this.config.styles) {
            mapOptions.styles = this.config.styles;
        }
        this.gmap = new google.maps.Map(this.mapContainer.find(".container")[0], mapOptions);
        if (this.mapContainer.find(".mapDir")) {
            this.directionsDisplay.setMap(this.gmap);
            this.directionsDisplay.setPanel(this.mapContainer.find(".mapDir")[0]);
        }

        // zoom = null: load all markers
        // zoom = 1-13: load displayed markers
        // zoom = [a, b, x, y]: load displayed markers, but not on first idle because we need to initialise with zoom = 1-13
        if (typeof this.config.zoom == 'object'
            && this.config.zoom != null
            && this.config.zoom[0] && this.config.zoom[1]
            && this.config.zoom[2] && this.config.zoom[3]
        ) {
            var bounds = new google.maps.LatLngBounds();
            bounds.extend(new google.maps.LatLng(this.config.zoom[0], this.config.zoom[1]));
            bounds.extend(new google.maps.LatLng(this.config.zoom[2], this.config.zoom[3]));
            this.gmap.fitBounds(bounds);
        }

        if (typeof this.config.markers == 'string') {
            if (this.config.zoom == null) {
                /* 1. Wait till map is ready: first idle.
                 * 2. Then load all markers.
                 * */
                google.maps.event.addListenerOnce(this.gmap, "idle",
                    $.proxy(this._loadAllMarkers, this, []));
            } else {
                google.maps.event.addListener(this.gmap, "idle",
                    $.proxy(this._reloadMarkersOnMapChange, this, []));
            }
        } else {
            for (var i = 0; i < this.config.markers.length; i++) {
                this.addMarker(this.config.markers[i]);
            }
        }

        // Opens the first InfoWindow. Must be deferred, because there were
        // problems opening InfoWindows in multiple maps on one site
        var showNextWindow = function() {
            for (var i = 0; i < this.markers.length; i++) {
                if (this.markers[i].kwfConfig.autoOpenInfoWindow) {
                    this.showWindow(this.markers[i]);
                }
            }
        };

        setTimeout($.proxy(showNextWindow, this), 1);
        this.fireEvent('show', this);
    },

    on: function(event, cb, scope)
    {
        if (typeof scope != 'undefined') cb = cb.bind(scope);
        this.mapContainer.on('kwfUp-map-'+event, cb);
    },

    one: function(event, cb, scope)
    {
        if (typeof scope != 'undefined') cb = cb.bind(scope);
        this.mapContainer.one('kwfUp-map-'+event, cb);
    },

    fireEvent: function(event, obj)
    {
        this.mapContainer.trigger('kwfUp-map-'+event, obj);
    },

    _loadAllMarkers: function()
    {
        /* 1. Add listener for markers finished loading
         * 2. Load markers
         * 3. Center all markers
         * 4. Add listener for further map-movements
         */
        this.one('reload', function () {
            // Listener for centering view to all shown markers on first load
            if (this.markers.length == 0) return;
            // Calculate center of all markers via google-map
            var latlngbounds = new google.maps.LatLngBounds();
            for (var i = 0; i < this.markers.length; i++) {
                if (this.markers[i].kwfConfig.isLightMarker) {
                    latlngbounds.extend(this.markers[i].getPosition());
                }
            }

            this.gmap.setCenter(latlngbounds.getCenter());
            this.gmap.fitBounds(latlngbounds);

            if (this.markers.length === 1 && this.config.singleMarkerZoom) {
                this.gmap.setZoom(this.config.singleMarkerZoom);
            }

            google.maps.event.addListener(this.gmap, "idle",
                $.proxy(this._reloadMarkersOnMapChange, this, []));
        }, this);

        this._reloadMarkers($.extend({}, this._baseParams));
    },

    centerMarkersIntoView: function()
    {
        google.maps.event.clearListeners(this.gmap, "idle");
        this._loadAllMarkers();
    },

    _reloadMarkersOnMapChange: function()
    {
        var params = $.extend({}, this._baseParams);
        var bounds = this.gmap.getBounds();
        params.lowestLng = bounds.getSouthWest().lng();
        params.lowestLat = bounds.getSouthWest().lat();
        params.highestLng = bounds.getNorthEast().lng();
        params.highestLat = bounds.getNorthEast().lat();
        this._reloadMarkers(params);
    },

    lastAjaxRequest: null,
    _reloadMarkers: function(params)
    {
        if (!this.gmapLoader) {
            $(this.mapContainer).append('<div id="gmapLoader">'+ __trlKwf('Loading...')+'</div>')
            this.gmapLoader = this.mapContainer.find('#gmapLoader');
        }
        this.gmapLoader.show();

        // abort old ajax-request to prevent problems with loading markers finishing in wrong order
        if (this.lastAjaxRequest) this.lastAjaxRequest.abort();
        this.lastAjaxRequest = $.ajax({
            url: this.config.markers,
            data: params,
            success: (function(response, options, result) {
                var reuseMarkers = [];
                var newMarkers = [];
                for (var a = 0; a < result.responseJSON.markers.length; a++) {
                    var marker = result.responseJSON.markers[a];
                    var doAdd = true;
                    for (var i = 0; i < this.markers.length; i++) {
                        if (this.markers[i].kwfConfig.latitude == marker.latitude
                            && this.markers[i].kwfConfig.longitude == marker.longitude
                            && this.markers[i].kwfConfig.isLightMarker == marker.isLightMarker
                        ) {
                            reuseMarkers.push(this.markers[i]);
                            doAdd = false;
                            break;
                        }
                    }
                    if (doAdd) newMarkers.push(marker);
                }

                for (var i = 0; i < this.markers.length; i++) {
                    if (reuseMarkers.indexOf(this.markers[i]) == -1) {
                        this.markers[i].setMap(null);
                    }
                }
                this.markers = reuseMarkers;
                for (var i = 0; i < newMarkers.length; i++) {
                    this.addMarker(newMarkers[i]);
                }
                onReady.callOnContentReady(this.mapContainer, {newRender: true});
                this.gmapLoader.hide();
                this.fireEvent('reload', this);
            }).bind(this)
        });
    },

    setBaseParams: function(params)
    {
        this._baseParams = params;
    },

    getBaseParams: function()
    {
        return this._baseParams;
    },

    addMarker : function(markerConfig)
    {
        var marker = this.createMarker(markerConfig);
        marker.kwfConfig = markerConfig;
        marker.setMap(this.gmap);
        this.markers.push(marker);
        if (markerConfig.infoHtml) {
            google.maps.event.addListener(marker, 'click', $.proxy(this.toggleWindow, this, marker));
        }
    },

    createMarker : function(markerConfig)
    {
        var gmarkCfg = { draggable: false };
        if (markerConfig.draggable) gmarkCfg.draggable = true;
        var image = this.getMarkerIcon(markerConfig);
        var myLatLng = new google.maps.LatLng(parseFloat(markerConfig.latitude), parseFloat(markerConfig.longitude));
        return new google.maps.Marker({
            position: myLatLng,
            icon: image
        });
    },

    getMarkerIcon : function(markerConfig)
    {
        var image = '';
        if (markerConfig.isLightMarker && this.config.lightMarkerSrc) {
            image = this.config.lightMarkerSrc;
        } else if (this.config.markerSrc) {
            image = this.config.markerSrc;
        }
        return image;
    },

    /** For images in marker popup **/
    markerWindowReady: function() {
        onReady.callOnContentReady(this.mapContainer, {newRender: true});
    },

    /**
     * @param marker: The marker with 'kwfConfig' property inside
     */
    showWindow : function(marker) {
        marker.infoWindow = new google.maps.InfoWindow();
        if (marker.kwfConfig.infoHtml && marker.kwfConfig.infoHtml != ""
            && "<br />" != marker.kwfConfig.infoHtml.toLowerCase()
        ) {
            marker.infoWindow.setContent(marker.kwfConfig.infoHtml);
            marker.infoWindow.open(marker.map, marker);
        }
        google.maps.event.addListener(marker.infoWindow, 'domready', $.proxy(this.markerWindowReady, this, [ marker ]));
    },
    closeWindow: function(marker) {
        marker.infoWindow.close();
    },
    toggleWindow: function(marker) {
        if (marker.infoWindow && marker.infoWindow.getMap() !== null && typeof marker.infoWindow.getMap() !== "undefined") {
            this.closeWindow(marker);
        } else {
            this.showWindow(marker);
        }
    },

    setMapDir : function (fromAddress) {
        var end = new google.maps.LatLng(parseFloat(this.config.latitude), parseFloat(this.config.longitude));
        var request = {
            origin:fromAddress,
            destination:end,
            travelMode: google.maps.TravelMode.DRIVING
        };
        this.directionsService.route(request, $.proxy(this._directionsCallback, this));
    },
    _directionsCallback: function(response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            this.directionsDisplay.setDirections(response);
            setTimeout((function() {
                onReady.callOnContentReady(this.directionsDisplay.getPanel(), { newRender: true});
            }).bind(this), 1);
        } else {
            alert(__trlKwf('Entered place could not been found!'));
        }
    }
};

module.exports = Map;
