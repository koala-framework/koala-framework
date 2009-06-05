Vps.onContentReady(function() {
    var clicked = [];
    var btns = Ext.query('form button.submit', document);
    Ext.each(btns, function(btn) {
        btn = Ext.get(btn);
        btn.on('click', function(e) {
            for(var i=0; i<clicked.length; i++) {
                if (clicked[i] == this) {
                    e.stopEvent();
                    return;
                }
            }
            clicked.push(this);
        });
    });
});
