var onReady = require('kwf/commonjs/on-ready');
window.$ = require('jquery'); //leak for qunit

$(function() {
    $('#show').click(function() {
        $('.foo').show();
        onReady.callOnContentReady(document.body, { action: 'show' });
    });
    $('#hide').click(function() {
        $('.foo').hide();
        onReady.callOnContentReady(document.body, { action: 'hide' });
    });
});

onReady.onShow('.foo', function(el) {
    $('#log').append('show');
});
onReady.onHide('.foo', function(el) {
    $('#log').append('hide');
});
