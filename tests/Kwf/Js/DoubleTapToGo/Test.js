var $ = require('jquery');
var QUnit = require('qunit');

var templates = {
    one:
        '<div class="mainMenu">'+
            '<ul class="menu">' +
                '<li class="hasSubmenu">' +
                    '<a href="#testlink">Testlink</a>'+
                    '<div class="dropdown">' +
                        '<ul class="subMenu">' +
                            '<li class="hasSubmenu">' +
                                '<a href="#testsublink">Test Sub Link</a>'+
                                '<div class="dropdown">' +
                                    '<ul class="subMenu">' +
                                        '<li>' +
                                            '<a href="#testsubsublink">Test Sub Sub Link</a>'+
                                        '</li>'+
                                    '</ul>'+
                                '</div>'+
                            '</li>'+
                            '<li class="hasSubmenu">' +
                                '<a href="#testsublink2">Test Sub Link 2</a>'+
                                '<div class="dropdown">' +
                                    '<ul class="subMenu">' +
                                        '<li>' +
                                            '<a href="#testsubsublink2">Test Sub Sub Link 2</a>'+
                                        '</li>'+
                                    '</ul>'+
                                '</div>'+
                            '</li>'+
                        '</ul>'+
                    '</div>'+
                '</li>'+
            '</ul>'+
        '</div>',
    two:
        '<div class="mainMenu">'+
            '<ul class="menu">' +
                '<li class="hasSubmenu">' +
                    '<a href="#testlink">Testlink</a>'+
                    '<div class="dropdown">' +
                        '<ul class="subMenu">' +
                            '<li class="hasSubmenu">' +
                                '<a href="#testsublink">Test Sub Link</a>'+
                                '<div class="dropdown">' +
                                    '<ul class="subMenu">' +
                                        '<li class="hasSubmenu">' +
                                            '<a href="#testsubsublink">Test Sub Sub Link</a>'+
                                            '<div class="dropdown">' +
                                                '<ul class="subMenu">' +
                                                    '<li>' +
                                                        '<a href="#testsubsubsublink">Test Sub Sub Sub Link</a>'+
                                                    '</li>'+
                                                '</ul>'+
                                            '</div>'+
                                        '</li>'+
                                    '</ul>'+
                                '</div>'+
                            '</li>'+
                            '<li class="hasSubmenu">' +
                                '<a href="#testsublink2">Test Sub Link 2</a>'+
                                '<div class="dropdown">' +
                                    '<ul class="subMenu">' +
                                        '<li class="hasSubmenu">' +
                                            '<a href="#testsubsublink">Test Sub Sub Link 2</a>'+
                                            '<div class="dropdown">' +
                                                '<ul class="subMenu">' +
                                                    '<li>' +
                                                        '<a href="#testsubsubsublink">Test Sub Sub Sub Link 2</a>'+
                                                    '</li>'+
                                                '</ul>'+
                                            '</div>'+
                                        '</li>'+
                                    '</ul>'+
                                '</div>'+
                            '</li>'+
                        '</ul>'+
                    '</div>'+
                '</li>'+
            '</ul>'+
        '</div>',
    three:
        '<div class="mainMenu">'+
            '<ul class="menu">' +
                '<li class="hasSubmenu">' +
                    '<a href="#testlink">Testlink</a>'+
                    '<div class="dropdown">' +
                        '<ul class="subMenu">' +
                            '<li class="hasSubmenu">' +
                                '<a href="#testsublink">Test Sub Link</a>'+
                                '<div class="dropdown">' +
                                    '<ul class="subMenu">' +
                                        '<li>' +
                                            '<a href="#testsubsublink">Test Sub Sub Link</a>'+
                                        '</li>'+
                                    '</ul>'+
                                '</div>'+
                            '</li>'+
                            '<li>' +
                                '<a href="#testsublink2">Test Sub Link 2</a>'+
                            '</li>'+
                        '</ul>'+
                    '</div>'+
                '</li>'+
                '<li class="hasSubmenu">' +
                    '<a href="#testlink2">Testlink 2</a>'+
                    '<div class="dropdown">' +
                        '<ul class="subMenu">' +
                            '<li class="hasSubmenu">' +
                                '<a href="#testlink2sublink">Test Sub Link</a>'+
                                '<div class="dropdown">' +
                                    '<ul class="subMenu">' +
                                        '<li>' +
                                            '<a href="#testlink2subsublink">Test Sub Sub Link</a>'+
                                        '</li>'+
                                    '</ul>'+
                                '</div>'+
                            '</li>'+
                        '</ul>'+
                    '</div>'+
                '</li>'+
            '</ul>'+
        '</div>'
};

//from http://stackoverflow.com/a/17789929/781662
if (window._phantom) {
  // Patch since PhantomJS does not implement click() on HTMLElement. In some
  // cases we need to execute the native click on an element. However, jQuery's
  // $.fn.click() does not dispatch to the native function on <a> elements, so we
  // can't use it in our implementations: $el[0].click() to correctly dispatch.
  if (!HTMLElement.prototype.click) {
    HTMLElement.prototype.click = function() {
      var ev = document.createEvent('MouseEvent');
      ev.initMouseEvent(
          'click',
          /*bubble*/true, /*cancelable*/true,
          window, null,
          0, 0, 0, 0, /*coordinates*/
          false, false, false, false, /*modifier keys*/
          0/*button=left*/, null
      );
      this.dispatchEvent(ev);
    };
  }
}

QUnit.asyncTest("Simulate touch-device and click-trough Levels : 2", function( assert ) {
    expect( 5 );

    $('#main').html($(templates.one));

    Kwf.Utils.DoubleTapToGo($('ul.menu > li'), {targetToOpen: '> div.dropdown'});
    Kwf.Utils.DoubleTapToGo($('ul.menu > li > div > ul > li'), {targetToOpen: '> div.dropdown'});

    var firstLinkTop = $('ul.menu > li:first > a');
    var firstLinkFirstSubLink = $('ul.menu > li:first > div.dropdown > ul > li:first > a');
    var firstLinkSecondSubLink = $('ul.menu > li:first > div.dropdown > ul > li:last > a');

    firstLinkTop.on('click', function(e) {
        setTimeout(function() {
            assert.ok(window.location.hash === $(e.target).attr('href') ? true : false , 'Check if preventDefault is active (Double Taped first Link)');
        }, 200);
    });

    firstLinkTop.trigger('touchend');
    assert.ok( $(firstLinkTop).next('.dropdown').is(':visible') ? true : false, 'First link (main) visible');

    firstLinkFirstSubLink.trigger('touchend');
    assert.ok( $(firstLinkFirstSubLink).next('.dropdown').is(':visible') ? true : false, 'First link > first sub link visible');

    firstLinkSecondSubLink.trigger('touchend');
    assert.ok( $(firstLinkFirstSubLink).next('.dropdown').is(':hidden') ? true : false, 'First link > first sub link hidden' );
    assert.ok( $(firstLinkSecondSubLink).next('.dropdown').is(':visible') ? true : false, 'First link > second sub link visible' );

    firstLinkTop.get(0).click();

    setTimeout(function() {
        QUnit.start();
    }, 300);
});


QUnit.asyncTest("Simulate touch-device and click-trough  Levels : 3", function( assert ) {
    expect( 7 );

    $('#main').html($(templates.two));

    Kwf.Utils.DoubleTapToGo($('ul.menu > li'), {targetToOpen: '> div.dropdown'});
    Kwf.Utils.DoubleTapToGo($('ul.menu > li > div > ul > li'), {targetToOpen: '> div.dropdown'});
    Kwf.Utils.DoubleTapToGo($('ul.menu > li > div > ul > li > div > ul > li'), {targetToOpen: '> div.dropdown'});

    var firstLinkTop = $('ul.menu > li:first > a');
    var firstLinkFirstSubLink = $('ul.menu > li:first > div.dropdown > ul > li:first > a');
    var firstLinkFirstSubSubLink = $('ul.menu > li:first > div.dropdown > ul > li:first > div.dropdown > ul > li:first > a');
    var firstLinkSecondSubLink = $('ul.menu > li:first > div.dropdown > ul > li:last > a');
    var firstLinkSecondSubSubLink = $('ul.menu > li:first > div.dropdown > ul > li:last > div.dropdown > ul > li > a');


    firstLinkTop.on('click', function(e) {
        setTimeout(function() {
            assert.ok(window.location.hash === $(e.target).attr('href') ? true : false , 'Check if preventDefault is active (Double Taped first Link)');
        }, 200);
    });

    firstLinkTop.closest('li').triggerHandler('touchend');
    assert.ok( $(firstLinkTop).next('.dropdown').is(':visible') ? true : false, 'First link (main) visible');

    firstLinkFirstSubLink.trigger('touchend');
    assert.ok( $(firstLinkFirstSubLink).next('.dropdown').is(':visible') ? true : false, 'First link > first sub link visible');


    firstLinkFirstSubSubLink.trigger('touchend');
    assert.ok( $(firstLinkFirstSubSubLink).next('.dropdown').is(':visible') ? true : false, 'First link > first sub sub link visible');

    firstLinkSecondSubLink.trigger('touchend');
    assert.ok( $(firstLinkFirstSubLink).next('.dropdown').is(':hidden') ? true : false, 'First link > first sub link hidden' );
    assert.ok( $(firstLinkSecondSubLink).next('.dropdown').is(':visible') ? true : false, 'First link > second sub link visible' );

    firstLinkSecondSubSubLink.trigger('touchend');
    assert.ok( $(firstLinkSecondSubSubLink).next('.dropdown').is(':visible') ? true : false, 'First link > second sub sub link visible');


    firstLinkTop.get(0).click();

    setTimeout(function() {
        QUnit.start();
    }, 300);
});

QUnit.asyncTest('Simulate touch-device and click through between menu links', function(assert) {
    expect( 7 );

    $('#main').html($(templates.three));

    Kwf.Utils.DoubleTapToGo($('ul.menu > li'), {targetToOpen: '> div.dropdown'});
    Kwf.Utils.DoubleTapToGo($('ul.menu > li > div > ul > li'), {targetToOpen: '> div.dropdown'});


    var firstLinkTop = $('ul.menu > li:first > a');
    var firstLinkFirstSubLink = $('ul.menu > li:first > div.dropdown > ul > li:first > a');
    var secondLinkTop = $('ul.menu > li:last > a');
    var secondLinkFirstSubLink = $('ul.menu > li:last > div.dropdown > ul > li:first > a');


    secondLinkFirstSubLink.on('click', function(e) {
        setTimeout(function() {
            assert.ok(window.location.hash === $(e.target).attr('href') ? true : false , 'Check if preventDefault is active (Double Taped second sub Link)');
        }, 200);
    });


    firstLinkTop.trigger('touchend');
    assert.ok( $(firstLinkTop).next('.dropdown').is(':visible') ? true : false, 'First link -> Level 1 -> visible');
    firstLinkFirstSubLink.trigger('touchend');
    assert.ok( $(firstLinkFirstSubLink).next('.dropdown').is(':visible') ? true : false, 'First link -> Level 1.1 -> visible');

    secondLinkTop.trigger('touchend');
    assert.ok( $(firstLinkTop).next('.dropdown').is(':hidden') ? true : false, 'First link -> Level 1 -> hidden');
    assert.ok( $(firstLinkFirstSubLink).next('.dropdown').is(':hidden') ? true : false, 'First link -> Level 2 -> hidden');
    assert.ok( $(secondLinkTop).next('.dropdown').is(':visible') ? true : false, 'Second link -> Level 1 -> visible');

    secondLinkFirstSubLink.trigger('touchend');
    assert.ok( $(secondLinkFirstSubLink).next('.dropdown').is(':visible') ? true : false, 'Second link -> Level 2 -> visible');

    secondLinkFirstSubLink.get(0).click();

    setTimeout(function() {
        QUnit.start();
    }, 300);
});
