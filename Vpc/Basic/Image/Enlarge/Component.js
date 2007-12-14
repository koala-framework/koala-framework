Ext.onReady(function()
{
    var els = document.getElementsByTagName('a');
    Ext.each(els, function(el) {
        var m = el.rel.match(/enlarge_[0-9]+_[0-9]+/);
        if (m) {
            el = new Ext.fly(el);
            el.on('click', function(e) {
                e.stopEvent();
                var m = this.dom.rel.match(/enlarge_([0-9]+)_([0-9]+)/);
                var dlg = new Ext.Window({
                    width: m[1],
                    height: m[2],
                    html: '<img src="'+this.dom.href+'" />'
                });
                dlg.show();
            });
        }
    });
});
