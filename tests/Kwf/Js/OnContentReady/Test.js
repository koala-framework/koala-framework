var $ = require('jQuery');
var QUnit = require('qunit');

function createIframe(url, loadCallback) {
    var url = '/kwf/test/kwf_js_on-content-ready_test' + url;
    $('#iframe-container').html(
        '<iframe src="'+url+'" />'
    );
    var win = $('#iframe-container').find('iframe').get(0).contentWindow;
    $(win).on('load', function() {
        setTimeout(function() {
            loadCallback.call(win, win.$);
        }, 100);
    });
    return win;
}

QUnit.test("basic test", function( assert ) {
    var done = assert.async();
    assert.expect( 1 );

    createIframe('/page1', function($) {
        assert.equal($('.foo').html(), 'bar');
        done();
    });
});

QUnit.test("show hide", function( assert ) {
    var done = assert.async();
    assert.expect( 1 );

    createIframe('/page2', function($) {
        $('#hide').trigger('click');
        $('#show').trigger('click');
        assert.equal($('#log').html(), 'showhideshow');
        done();
    });
});

