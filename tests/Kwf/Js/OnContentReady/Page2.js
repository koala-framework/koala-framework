var onReady = require('kwf/on-ready');

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
