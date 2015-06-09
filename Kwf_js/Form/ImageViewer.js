Kwf.Form.ImageViewer = Ext2.extend(Kwf.Form.ShowField,
{
    tpl: '<tpl if="previewUrl">'+
            '<tpl if="imageUrl">'+
                '<a href="{imageUrl}" target="_blank">'+
            '</tpl>'+
            '<img src="{previewUrl}" />'+
            '<tpl if="imageUrl">'+
                '</a>'+
            '</tpl>'+
         '</tpl>'
});
Ext2.reg('imageviewer', Kwf.Form.ImageViewer);
