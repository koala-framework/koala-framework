Vps.Form.ImageViewer = Ext.extend(Vps.Form.ShowField,
{
    tpl: '<tpl if="previewUrl">'+
            '<tpl if="imageUrl">'+
                '<a href="{imageUrl}?{[Math.random()]}" target="_blank">'+
            '</tpl>'+
            '<img src="{previewUrl}?{[Math.random()]}" />'+
            '<tpl if="imageUrl">'+
                '</a>'+
            '</tpl>'+
         '</tpl>'
});
Ext.reg('imageviewer', Vps.Form.ImageViewer);
