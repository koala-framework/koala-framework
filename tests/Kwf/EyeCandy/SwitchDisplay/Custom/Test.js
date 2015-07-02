var onReady = require('kwf/on-ready');

onReady.onRender('.customClass', function(el) {
    Kwf.EyeCandy.Switch.Display(el, {container: '.customContent', link: '.customLink'});
} , {defer: true});


QUnit.asyncTest("Switch Display - Click Open (Async Container Show)", function( assert ) {
    expect( 2 );
    var switchLink = $('.first .customLink');
    $(switchLink).trigger('click');

    setTimeout(function() {
        var container = $('.first .customContent');
        assert.ok( $(container).is(':visible') ? true : false, 'Container visible' );
    }, 500);

    setTimeout(function() {
        $(switchLink).trigger('click');
    }, 1500);

    setTimeout(function() {
        var container = $('.first .customContent');
        assert.ok( $(container).is(':hidden') ? true : false, 'Container hidden' );
        QUnit.start();
    }, 3000);
});
