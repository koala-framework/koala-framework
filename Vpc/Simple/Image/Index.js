Ext.namespace('Vpc.Simple.Image');
Vpc.Simple.Image.Index = function(renderTo, config)
{
	config = config || {};
	config = Ext.applyIf(config, { fileUpload: true })
	this.form = new Vps.Auto.Form(renderTo, config);
	console.log(config);
	this.form.on('loaded', function(form, action) {
		if (action.result.path != ''){
			this.img = this.form.el.createChild("Vorschau: <br><a href="+ action.result.pathbig +" target = \"_blank\"><img src="+ action.result.path +"></a>");
		} else {
			this.img = this.form.el.createChild("<div> Es wurde noch kein Bild hochgeladen </div>");
		}
	});
	this.form.on('dataChanged', function(result) {
		this.img.remove();
		this.img = this.form.el.createChild("<a href="+ result.pathbig +" target = \"_blank\"><img src="+ result.path +"></a>");
	});
};

Ext.extend(Vpc.Simple.Image.Index, Ext.util.Observable,
{

});
