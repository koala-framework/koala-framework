Kwf.onJElementReady('.asyncContent', function(el) {
    el.html('Bar');
}, { checkVisibility: true });

QUnit.test("Switch Display - Container Init Hidden", function( assert ) {
    var container = $('.first .switchContent');
    assert.equal($(container).is(':hidden'), 1, 'Container initialized hidden');
});

QUnit.asyncTest("Switch Display - Click Open (Async Container Show)", function( assert ) {
    expect( 2 );
    var switchLink = $('.second .switchLink');
    $(switchLink).trigger('click');

    setTimeout(function() {
        var container = $('.second .switchContent');
        assert.ok( $(container).is(':visible') ? true : false, 'Container visible' );
    }, 500);

    setTimeout(function() {
        $(switchLink).trigger('click');
    }, 1000);

    setTimeout(function() {
        var container = $('.second .switchContent');
        assert.ok( $(container).is(':hidden') ? true : false, 'Container hidden' );
        QUnit.start();
    }, 1800);
});

QUnit.asyncTest('Switch Display - Async add / hide content', function ( assert ) {
    expect( 3 );

    var asyncContainer = $('.third .asyncContent');
    assert.ok( $(asyncContainer).html() == '', 'Aysnc Container is empty');

    var switchLink = $('.third .switchLink');
    $(switchLink).trigger('click');

    assert.ok( $(asyncContainer).html() == 'Bar', 'Container contains "Bar" and is visible');

    setTimeout(function() {
        var container = $('.third .switchContent');
        assert.ok( $(container).is(':visible') ? true : false, 'Container visible' );
        QUnit.start();
    }, 500);
});