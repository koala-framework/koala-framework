Vps.Form.ImageViewer = Ext.extend(Ext.form.Field,
{
    onRender : function(ct, position){
        this.el = ct.createChild('<span class="x-form-field" id="' + this.name + '"></span>', position);
        this.rendered = true;
        this.el.dom.name = this.name;
    },

    setValue : function(value)
    {
        var src = '<input style="visibility:hidden">';
        if (value.previewUrl) {
            var rand = Math.floor((Math.random()*1000000));
            src = '';
            if (value.imageUrl) { src += '<a href="' + value.imageUrl + '?' + rand + '" target="_blank">'; }
            src += '<img src="' + value.previewUrl + '?' + rand + '" />'
            if (value.imageUrl) { src += '</a>'; }
        }
        Ext.get(this.name).dom.innerHTML = src;
    }

});
Ext.reg('imageviewer', Vps.Form.ImageViewer);