// @require Ext4.window.Window
Ext4.onReady(function() {
    var w = new Ext4.window.Window({
        title: 'windowtitle',
        html: 'windowcontent 123 123 123'
    });
    w.show();
});
