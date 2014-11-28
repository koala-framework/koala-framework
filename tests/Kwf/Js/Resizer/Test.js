Kwf.onElementReady('.kwfResizeElement', function(element) {
    var resizer = new Kwf.Utils.Resizable(element.child('.resizeElement'), {
        handles: 'all',
        pinned: true,
        preserveRatio: true,
        constrainTo: element.child('.constraintBox'),
        width: 70,
        height: 70,
        minWidth: 20,
        minHeight: 20,
        maxWidth: 200,
        maxHeight: 200
    });
    resizer.on("resize", function(width, height, e) {
    }, this);
    new Ext.dd.DD(element.child('.resizeElement'), '');

    var window = new Ext.Window({
        title: trlKwf('Test'),
        closeAction: 'close',
        width: 200,
        height: 300,
        layout: 'fit',
        resizable: true,
        initComponent: function() {
            Ext.Window.superclass.initComponent.call(this);
        }
    });
    window.show();
});
