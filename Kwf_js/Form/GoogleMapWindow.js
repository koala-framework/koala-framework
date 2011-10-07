Vps.Form.GoogleMapWindow = Ext.extend(Ext.Window,
{
    initComponent: function() {
        this.addEvents('confirm');
        this.addEvents('clear');

        this.actions = {};
        this.actions.clear = new Ext.Action({
            text:trlVps('Clear'),
            handler: function() {
                this.clear = true;
                this.fireEvent('clear', this);
                this.hide();
            },
            icon    : '/assets/silkicons/bin_closed.png',
            cls     : 'x-btn-text-icon',
            scope : this}
        );

        this.tbar = [{}];
        this.tbar.add(
            '->', {
            text:trlVps('Search Location'),
            handler: this.addressPrompt,
            icon    : '/assets/silkicons/zoom.png',
            cls     : 'x-btn-text-icon',
            scope : this
        }, this.actions.clear);

        this.buttons = [
        {
            text: trlVps('Cancel'),
            handler: function() {
                this.hide();
            },
            scope: this
        },{
            text: trlVps('Ok'),
            handler: function() {
                this.clear = false;
                this.fireEvent('confirm', this);
                this.hide();
            },
            scope: this
        }];
        Vps.Form.GoogleMapWindow.superclass.initComponent.call(this);
    },
    afterRender:function(){
        Vps.Form.GoogleMapWindow.superclass.afterRender.call(this);
        this.map = new GMap2(this.body.dom);
        this.geocoder = new GClientGeocoder();
        this.map.addControl(new GLargeMapControl());
        this.buttons = [
        {
            text: trlVps('Cancel'),
            handler: function() {
                this.hide();
            },
            scope: this
        },{
            text: trlVps('OK'),
            handler: function() {
                this.clear = false;
                this.fireEvent('confirm', this);
                this.hide();
            },
            scope: this
        }];
        Vps.Form.GoogleMapWindow.superclass.initComponent.call(this);
    },
    afterRender:function(){
        Vps.Form.GoogleMapWindow.superclass.afterRender.call(this);
        Vps.GoogleMap.load(function() {
            this.map = new GMap2(this.body.dom);
            this.geocoder = new GClientGeocoder();
            this.map.addControl(new GLargeMapControl());
            this.map.addControl(new GScaleControl(), new GControlPosition(G_ANCHOR_BOTTOM_LEFT, new GSize(64,15)));
            this.map.addControl(new GMapTypeControl());
            this.map.addControl(new GOverviewMapControl());

            var point = new GLatLng(47.9534, 13.2448);
            this.marker = new GMarker(point, {draggable: true});
            //TODO falscher startort
            if (this.markerpoint_x) {
                this.setMarkerPoint(this.markerpoint_y+';'+this.markerpoint_x);
            } else {
                this.setMarkerPoint ('47.9534;13.2448');
            }

            GEvent.addListener(this.marker, 'click',     this.showLatLng.createDelegate(this));
            GEvent.addListener(this.marker, 'dragstart', this.hideLatLng.createDelegate(this));
            GEvent.addListener(this.marker, 'dragend',   this.showLatLng.createDelegate(this));
        }, this);
    },
    addressPrompt:function(){
        Ext.Msg.prompt(trlVps('enter address'), trlVps('Example')+': Pfongauerstraße 67, 5202 Neumarkt am Wallersee', function(btn, text){
            if (btn == 'ok' && text != ''){
                this.geoCodeLookup(text);
            }
        }, this);
    },
    geoCodeLookup : function(address) {
        this.geocoder.getLocations(address, this.addAddressToMap.createDelegate(this));
    },
    addAddressToMap : function(response) {
        this.placemarks = response.Placemark;
        if (!response || response.Status.code != 200) {
                Ext.MessageBox.alert(
                    trlVps('Error'),
                    trlVps('Code {0} Error Returned', [response.Status.code])
                );
            } else {
            place = response.Placemark[0];
            addressinfo = place.AddressDetails;
            accuracy = addressinfo.Accuracy;
            if (accuracy == 0) {
                Ext.MessageBox.alert(trlVps('Unknown address'), trlVps('Address could not be found'));
            }else{
                if (accuracy < 7) {
                    var myData = [];
                    for (var i=0; i<this.placemarks.length; i++) {
                        if (!this.placemarks[i]) { break; }
                        var tempPush = [this.placemarks[i].address];
                        myData.push(tempPush);
                    }

                    var store = new Ext.data.SimpleStore({
                        fields: [
                        {name: 'address'}
                        ]
                    });
                    store.loadData(myData);

                    var grid = new Ext.grid.GridPanel({
                        store:store,
                        columns: [
                            {id: 'address', header: trlVps("Adresses"), width: 300, sortable:false, dataIndex: 'address'}
                        ]
                    });
                    grid.on('rowdblclick', function(grid, index) {
                        win.close();
                        this.setMarkerPoint(this.placemarks[index].Point.coordinates[1]+';'+this.placemarks[index].Point.coordinates[0]);
                    }, this);
                    grid.on('rowmousedown', function(grid, index) {
                        this.setMarkerPoint(this.placemarks[index].Point.coordinates[1]+';'+this.placemarks[index].Point.coordinates[0]);
                    }, this);

                    var win = new Ext.Window({
                        modal: true,
                        title: trlVps('Possible Destinations'),
                        width:400,
                        height:250,
                        shadow:true,
                        closeAction: 'close',
                        layout: 'fit',
                        buttons: [{
                            text: 'Ok',
                            handler: function() {
                                win.close();
                            },
                            scope: this
                        }],
                        items: [grid]
                    });
                    win.show();
                } else {
                    this.setMarkerPoint(place.Point.coordinates[1]+';'+place.Point.coordinates[0]);
                }
            }
          }
    },
    showLatLng:function(){
        var pnt = this.marker.getPoint();
        pnt.y = Math.round(pnt.y * 100000000) / 100000000;
        pnt.x = Math.round(pnt.x * 100000000) / 100000000;
        this.markerpoint_x = pnt.x;
        this.markerpoint_y = pnt.y;
        this.marker.openInfoWindowHtml('<strong>'+trlVps('Move marker while pressing mousekey.')+'</strong><br /><br />' +
                trlVps('Latitude')+ ': ' +pnt.y +'<br />'+trlVps('Longitude')+ ': ' +pnt.x);
    },
    hideLatLng:function(){
        this.marker.closeInfoWindow();
    },
    setMarkerPoint:function(value){
        var points = value.split(";");
        this.markerpoint_y = points[0];
        this.markerpoint_x = points[1];
        if (this.markerpoint_y && this.markerpoint_x && this.map){
            this.map.setCenter(new GLatLng(this.markerpoint_y,this.markerpoint_x), 13);
            this.marker.setLatLng(new GLatLng(this.markerpoint_y,this.markerpoint_x));
            this.marker.closeInfoWindow();
            this.map.clearOverlays();
            this.map.addOverlay(this.marker);
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
        var pnt = this.marker.getPoint();
        pnt.y = Math.round(pnt.y * 100000000) / 100000000;
        pnt.x = Math.round(pnt.x * 100000000) / 100000000;
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