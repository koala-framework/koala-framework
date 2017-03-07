var onReady = require('kwf/commonjs/on-ready');

onReady.onRender('.customClass', function(el) {
   Kwf.EyeCandy.Switch.Display(el, {
       container: '.customContent',
       link: '.customLink',
       fade: true,
       hover: true
   });
} , {defer: true});

QUnit.asyncTest("Switch Display - Hover Open (Async Container Show)", function( assert ) {
    expect( 2 );
    var switchLink = $('.first .customLink');
    $(switchLink).mouseenter();

    setTimeout(function() {
        var container = $('.first .customContent');
        assert.ok( $(container).is(':visible') ? true : false, 'Container visible' );
    }, 1000);

    setTimeout(function() {
        $(switchLink).mouseleave();
    }, 1500);

    setTimeout(function() {
        var container = $('.first .customContent');
        assert.ok( $(container).is(':hidden') ? true : false, 'Container hidden' );
        QUnit.start();
    }, 2500);
});

QUnit.asyncTest("Switch Display - Hover Open - Stress Test (Async Container Hidden)", function( assert ) {
    expect( 1 );
    var switchLink = $('.second .customLink');

    var i = 0;

    var interval = setInterval(function(){

        i++;
        setTimeout(function(){
            $(switchLink).mouseenter();
        }, 10);

        setTimeout(function(){
            $(switchLink).mouseleave();
        }, 220);
        if(i === 30) {
            clearInterval(interval);
        }
    }, 450);

    setTimeout(function(){
        clearInterval(interval);
        var container = $('.second .customContent');
        assert.ok( $(container).is(':hidden') ? true : false, 'Container hidden' );
        QUnit.start();
    }, 16000);
});
