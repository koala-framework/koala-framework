window.onload = function() {
    Kwf.Loader.require('Ext4.panel.Panel', function() {
        Kwf.Loader.require('Ext4.grid.Panel', function() {
            Kwf.Loader.require('Ext4.tree.Panel', function() {
            });
            Kwf.Loader.require('Ext4.window.Window', function() {
                var w = new Ext4.window.Window({
                    title: 'windowtitle',
                    html: 'windowcontent 123 123 123'
                });
                w.show();
            });
        });
    });
    Kwf.Loader.require('Ext4.panel.Panel', function() {
    });
};
