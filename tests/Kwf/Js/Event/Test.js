var onReady = require('kwf/on-ready');

onReady.onContentReady(function() {
    var testEl = Ext2.DomQuery.select('#eventTest')[0];

    Kwf.Event.on(testEl, 'mouseEnter', function() {
        document.getElementById('result').innerHTML += 'mouseEnter: '+this.testScope+' ---';
    }, { testScope: 'enter' });

    Kwf.Event.on(testEl, 'mouseLeave', function() {
        document.getElementById('result').innerHTML += 'mouseLeave: '+this.testScope+' ---';
    }, { testScope: 'leave' });

});
