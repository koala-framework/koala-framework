Ext.namespace('Vpc.Simple.Image');
Vpc.Simple.Image.Index = function(renderTo, config)
{
    this.createImage = function()
    {
        if (this.img != undefined) {
            this.img.remove();
        }
        var rand = Math.floor((Math.random()*1000000));
        if (this.urlbig != undefined) {
            this.img = this.form.form.el.createChild("<div><a href="+ this.urlbig + "?" + rand + " target = \"_blank\"><img src="+ this.url + "?" + rand + "></a></div>");
        } else {
            this.img = this.form.form.el.createChild("<div>No File uploaded yet.</div>");
        }
    }

    this.form = new Vps.Auto.Form(renderTo, { fileUpload: true, controllerUrl: config.controllerUrl });
    this.form.on('loaded', function(form, action) {
        if (action.result.url != ''){
            this.urlbig = action.result.urlbig;
            this.url = action.result.url;
        }
        this.createImage();
    }, this);
    this.form.on('dataChanged', function(result) {
        this.createImage();
    }, this);
};

Ext.extend(Vpc.Simple.Image.Index, Ext.util.Observable,
{
});
