Vps.Form.GoogleMapsField = Ext.extend(Ext.form.TriggerField,
{
	triggerClass : 'x-form-search-trigger',
	readOnly : false,
	width : 200,
	onTriggerClick : function(){
        var win = Vps.Form.GoogleMapsField.GoogleMapWindow; //statische var, nur ein window erstellen??
        if (!win) {
            win = new Vps.Form.GoogleMapWindow({
				modal: true,
				title: trlVps('Select your Position'),
				width:535,
				height:500,
				shadow:true,
				closeAction: 'hide'
            });
            Vps.Form.GoogleMapsField.GoogleMapWindow = win;
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
    }
});
Ext.reg('googlemapsfield', Vps.Form.GoogleMapsField);
