var onReady = require('kwf/on-ready');

$(function() {
    $('#show').click(function() {
        $('.foo').show();
        Kwf.callOnContentReady(document.body, { action: 'show' });
    });
    $('#hide').click(function() {
        $('.foo').hide();
        Kwf.callOnContentReady(document.body, { action: 'hide' });
    });
});

onReady.onShow('.foo', function(el) {
    $('#log').append('show');
});
onReady.onHide('.foo', function(el) {
    $('#log').append('hide');
});
