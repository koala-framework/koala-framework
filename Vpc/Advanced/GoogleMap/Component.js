Vpc.Advanced.GoogleMap = function(mapContainer, options, text){
    var input = mapContainer.down("form.fromAddress input");
    mapContainer.down("form.fromAddress").on('submit', function(e) {
      this.setMapDir(input.getValue());
      e.stopEvent();
    }, this);
    this.mapContainer = mapContainer;
    this.options = options;
    this.text = text;

    var container = mapContainer.down(".container");
    container.setWidth(parseInt(options.width));
    container.setHeight(parseInt(options.height));

    var input = mapContainer.down("form.fromAddress input");
    input.dom.value = trlVps('Place of departure: zip code, Town, Street');
    //input.set({value:'Place of departure: zip code, Town, Street'}); // auskommentiert wegen problemen mit safari
    input.on('focus', function() {
      if (this.getValue() == trlVps('Place of departure: zip code, Town, Street')){
        /*this.set({
          value: ''
        });*/
        input.dom.value = '';
        this.removeClass('textBefore');
        this.addClass('textOn');
      }
    }, input);
    input.on('blur', function() {
      if (this.getValue()=='') {
        /*this.set({
          value:'Ihr Abfahrtsort: PLZ, Ort, Straße'});*/
          input.dom.value = trlVps('Place of departure: zip code, Town, Street');
          this.removeClass('textOn');
          this.addClass('textBefore');

      }
      this.addClass('textBefore');
    }, input);
};

Vpc.Advanced.GoogleMap.prototype = {

  show : function()
  {
    this.map = new GMap2(this.mapContainer.down(".container").dom);
    this.geocoder = new GClientGeocoder();
    if (this.options.zoom_properties == '0')
      this.map.addControl(new GLargeMapControl());
    else if (this.options.zoom_properties == '1')
      this.map.addControl(new GSmallMapControl());

    if (parseInt(this.options.scale))
          this.map.addControl(new GScaleControl(),
                new GControlPosition(G_ANCHOR_BOTTOM_LEFT, new GSize(64,15)));

    if (parseInt(this.options.satelite))
      this.map.addControl(new GMapTypeControl());

    if (parseInt(this.options.overview))
      this.map.addControl(new GOverviewMapControl());


    this.map.setCenter(new GLatLng(parseFloat(this.options.longitude),
                    parseFloat(this.options.latitude)),
                    parseInt(this.options.zoom));
                    this.mapDir = new GDirections(this.map,
                        this.mapContainer.down(".mapDir").dom);

  },

  activateMarker : function (){
    this.marker = new GMarker(new GLatLng(parseFloat(this.options.longitude),
                  parseFloat(this.options.latitude)), {draggable: false});
    this.map.addOverlay(this.marker);
    this.windowsize = parseInt(this.options.width) * 0.8;
    if (this.text != "" && "<br>" != this.text.toLowerCase()) {
      this.marker.openInfoWindowHtml(this.text, {maxWidth: this.windowsize});
    }

    GEvent.addListener(this.marker, 'click',this.showWindow.createDelegate(this));
  },

  showWindow : function (){
    if (this.text != "" && "<br>" != this.text.toLowerCase()) {
      this.marker.openInfoWindowHtml(this.text, {maxWidth: this.windowsize});
    }
  },

  setMapDir : function (fromAddress) {
    this.map.closeInfoWindow();
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
  useFrom : function(Placemark, rewriteInput){
    if (typeof Placemark != 'object') {
                    Placemark = this.suggestPlacemarks[Placemark];
                }
                var pos = Placemark.Point.coordinates[1] +','+ Placemark.Point.coordinates[0];
                this.mapDir.load('from: ' + pos + ' to: ' + this.options.longitude + ', ' +
                  this.options.latitude, { 'locale': 'de_AT' });
        if (rewriteInput) mapContainer.down("form.fromAddress").set({
          value: Placemark.address
        });
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
                        if (!Placemark[i]) { break; }
            var a = el.createChild({tag: 'li'}).createChild({tag: 'a',
                        href: '#', html: Placemark[i].address, rel:i});
            a.on('click', function(e, el){
              this.useFrom(Placemark[el.rel], true);
              e.stopEvent();
            }, this);
          }
                } else if (elParent) {
                    elParent.setStyle({display:"none"});
                }
  }
};

Vpc.Advanced.GoogleMap.renderedMaps = [];

Vps.onContentReady(function() {
    var maps = Ext.DomQuery.select('div.vpcAdvancedGoogleMap');
    Ext.each(maps, function(map) {
        if (Vpc.Advanced.GoogleMap.renderedMaps.indexOf(map) != -1) return;
        Vpc.Advanced.GoogleMap.renderedMaps.push(map);

        var mapContainer = new Ext.Element(map);
        var options = mapContainer.down(".options", true);
        if (!options) return;
        options = Ext.decode(options.value);
        var text = mapContainer.down("div.text").dom.innerHTML;
        var myMap = new Vpc.Advanced.GoogleMap(mapContainer, options, text);

        Vps.GoogleMap.load(function() {
            this.show();
            this.activateMarker();
        }, myMap);
    });
});

