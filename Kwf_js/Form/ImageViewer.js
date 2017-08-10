Kwf.Form.ImageViewer = Ext2.extend(Kwf.Form.ShowField,
{
    tpl: '<tpl if="previewUrl">'+
            '<tpl if="imageUrl">'+
                '<a href="{imageUrl:htmlEncode}" target="_blank">'+
            '</tpl>'+
            '<img src="{previewUrl:htmlEncode}" />'+
            '<tpl if="imageUrl">'+
                '</a>'+
            '</tpl>'+
         '</tpl>'
});
Ext2.reg('imageviewer', Kwf.Form.ImageViewer);
