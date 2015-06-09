Kwf.Form.GoogleMapsField = Ext2.extend(Ext2.form.TriggerField,
{
	triggerClass : 'x2-form-search-trigger',
	readOnly : false,
	width : 200,
	onTriggerClick : function(){
        Kwf.GoogleMap.load(function(){
        var win = Kwf.Form.GoogleMapsField.GoogleMapWindow; //statische var, nur ein window erstellen??
            if (!win) {
                win = new Kwf.Form.GoogleMapWindow({
                    modal: true,
                    title: trlKwf('Select your Position'),
                    width:535,
                    height:500,
                    shadow:true,
                    closeAction: 'hide'
                });
                Kwf.Form.GoogleMapsField.GoogleMapWindow = win;
            }
            win.purgeListeners();
            win.on('confirm', function(win, ch) {
                this.setValue(win.getMarkerPoint());
            }, this);
            win.on('clear', function(win, ch) {
                this.setValue("");
            }, this);
            if (this.allowBlank){
                win.setHideClearButton(false);
            } else {
                win.setHideClearButton(true);
            }
            win.show();
            win.setMarkerPoint(this.getValue());
        }, this);
    }
});
Ext2.reg('googlemapsfield', Kwf.Form.GoogleMapsField);
