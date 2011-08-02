
Vps.onContentReady(function() {
    var testEl = Ext.DomQuery.select('#eventTest')[0];

    Vps.Event.on(testEl, 'mouseEnter', function() {
        document.getElementById('result').innerHTML += 'mouseEnter: '+this.testScope+' ---';
    }, { testScope: 'enter' });

    Vps.Event.on(testEl, 'mouseLeave', function() {
        document.getElementById('result').innerHTML += 'mouseLeave: '+this.testScope+' ---';
    }, { testScope: 'leave' });

});
