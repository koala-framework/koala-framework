Kwf.Form.GoogleMapWindow = Ext2.extend(Ext2.Window,
{
    initComponent: function() {
        this.addEvents('confirm');
        this.addEvents('clear');

        this.actions = {};
        this.actions.clear = new Ext2.Action({
            text:trlKwf('Clear'),
            handler: function() {
                this.clear = true;
                this.fireEvent('clear', this);
                this.hide();
            },
            icon    : KWF_BASE_URL+'/assets/silkicons/bin_closed.png',
            cls     : 'x2-btn-text-icon',
            scope : this}
        );

        this.tbar = [{}];
        this.tbar.add(
            '->', {
            text:trlKwf('Search Location'),
            handler: this.addressPrompt,
            icon    : KWF_BASE_URL+'/assets/silkicons/zoom.png',
            cls     : 'x2-btn-text-icon',
            scope : this
        }, this.actions.clear);

        this.buttons = [
        {
            text: trlKwf('Cancel'),
            handler: function() {
                this.hide();
            },
            scope: this
        },{
            text: trlKwf('Ok'),
            handler: function() {
                this.clear = false;
                this.fireEvent('confirm', this);
                this.hide();
            },
            scope: this
        }];
        Kwf.Form.GoogleMapWindow.superclass.initComponent.call(this);
    },
    afterRender:function(){
        var startLatLng = new google.maps.LatLng(47.8904081, 13.1834356);
        Kwf.Form.GoogleMapWindow.superclass.afterRender.call(this);
        Kwf.GoogleMap.Loader.load(function() {
            var mapOptions = {
                center: startLatLng,
                zoom: parseInt(8),
                zoomControl: true,
                scaleControl: true,
                mapTypeControl: true,
                overviewMapControl: true
            };
            this.map = new google.maps.Map(this.body.dom,
                mapOptions);
            this.geocoder = new google.maps.Geocoder();

            this.marker =  new google.maps.Marker({
                position: startLatLng,
                draggable: true
            });
            this.marker.setMap(this.map);
            this.marker.infoWindow = new google.maps.InfoWindow();

            google.maps.event.addListener(this.marker, 'click',     this.showLatLng.createDelegate(this));
            google.maps.event.addListener(this.marker, 'dragstart', this.hideLatLng.createDelegate(this));
            google.maps.event.addListener(this.marker, 'dragend',   this.showLatLng.createDelegate(this));

            this.showLatLng();
        }, this);
    },
    addressPrompt:function(){
        Ext2.Msg.prompt(trlKwf('enter address'), trlKwf('Example')+': Landesstraße 23, 5302 Henndorf am Wallersee', function(btn, text){
            if (btn == 'ok' && text != ''){
                this.geoCodeLookup(text);
            }
        }, this);
    },
    geoCodeLookup : function(address) {
        this.geocoder.geocode( { 'address': address}, this.addAddressToMap.createDelegate(this));
    },
    addAddressToMap : function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            var newLocation = results[0].geometry.location;
            this.marker.setPosition(newLocation);
            this.map.setCenter(newLocation);
            this.showLatLng();
        } else {
            alert(trlKwf('Entered place could not been found!'));
        }
    },
    showLatLng:function(){
        var pnt = this.marker.getPosition();
        pnt.y = Math.round(pnt.lat() * 100000000) / 100000000;
        pnt.x = Math.round(pnt.lng() * 100000000) / 100000000;
        this.markerpoint_x = pnt.x;
        this.markerpoint_y = pnt.y;
        this.marker.infoWindow.setContent('<strong>'+trlKwf('Move marker while pressing mousekey.')+'</strong><br /><br />' +
                trlKwf('Latitude')+ ': ' +pnt.y +'<br />'+trlKwf('Longitude')+ ': ' +pnt.x);
        this.marker.infoWindow.open(this.map, this.marker);
    },
    hideLatLng:function(){
        this.marker.infoWindow.close();
    },
    setMarkerPoint:function(value){
        var points = value.split(";");
        this.markerpoint_y = points[0];
        this.markerpoint_x = points[1];
        var latLng = new google.maps.LatLng(this.markerpoint_y,this.markerpoint_x);
        if (this.markerpoint_y && this.markerpoint_x && this.map){
            this.map.setCenter(latLng, 13);
            this.marker.setPosition(latLng);
//            this.map.clearOverlays();
//            this.map.addOverlay(this.marker);
            this.showLatLng();
        } else if (!this.markerpoint_y && !this.markerpoint_y) {
            this.addressPrompt();
        }

    },
    getMarkerPoint:function(){
        if (this.clear == true){
            this.clear = false;
            return "";
        }
        var pnt = this.marker.getPosition();
        pnt.y = Math.round(pnt.lat() * 100000000) / 100000000;
        pnt.x = Math.round(pnt.lng() * 100000000) / 100000000;
        return  pnt.y +';'+pnt.x;
    },
    setHideClearButton:function(check){
        if (check) {
            this.actions.clear.hide();
        } else {
            this.actions.clear.show();
        }
    }
});
