Ext.namespace('Vpc.Basic.Image');
Vpc.Basic.Image.Index = Ext.extend(Vps.Auto.FormPanel,
{
    initComponent : function()
    {
        Vpc.Basic.Image.Index.superclass.initComponent.call(this);
        this.on('loaded', function(form, action) {
            if (action.result.url != ''){
                this.urlbig = action.result.urlbig;
                this.url = action.result.url;
            }
            this.createImage();
        }, this);
        this.on('dataChanged', function(result) {
            this.createImage();
        }, this);
    },

    createImage : function()
    {
        if (this.img != undefined) {
            this.img.remove();
        }
        var rand = Math.floor((Math.random()*1000000));
        if (this.urlbig != undefined) {
            this.img = this.form.el.createChild("<div><a href="+ this.urlbig + "?" + rand + " target = \"_blank\"><img src="+ this.url + "?" + rand + "></a></div>");
        } else {
            this.img = this.form.el.createChild("<div>No File uploaded yet.</div>");
        }
    }

});
